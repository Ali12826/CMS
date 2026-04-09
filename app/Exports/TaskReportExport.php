<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class TaskReportExport implements FromCollection, WithHeadings, WithMapping, WithDrawings, WithEvents, ShouldAutoSize
{
    protected $tasks;

    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function collection()
    {
        return $this->tasks;
    }

    // --- 1. HEADERS ---
    public function headings(): array
    {
        return [
            'TASK ID',
            'SUBJECT',
            'TASK',
            'LOCATION',
            'DEPARTMENT',
            'PERFORMED BY',
            'ASSIGNED BY',   // <--- NEW COLUMN ADDED HERE
            'ENTRY DATE',
            'START TIME',
            'END TIME',
            'STATUS'
        ];
    }

    // --- 2. DATA MAPPING ---
    public function map($task): array
    {
        $statusMap = [0 => 'Incomplete', 1 => 'In Progress', 2 => 'Completed'];

        $startTime = $task->t_start_time ? date('h:i A', strtotime($task->t_start_time)) : '';
        $endTime   = $task->t_end_time   ? date('h:i A', strtotime($task->t_end_time))   : '';

        return [
            $task->task_id,
            $task->t_title,
            $task->t_description,
            $task->location ?? '',
            $task->employee_dept_name ?? '',
            $task->assigned_to_name ?? '',
            $task->assigned_by_name ?? '', // <--- NEW DATA MAPPED HERE
            date('d-M-Y', strtotime($task->t_start_time)),
            $startTime,
            $endTime,
            $statusMap[$task->status] ?? 'Unknown',
        ];
    }

    // --- 3. LOGO ---
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setPath(public_path('storage/images/logo2.png'));
        $drawing->setHeight(100);
        $drawing->setCoordinates('B2');
        return [$drawing];
    }

    // --- 4. STYLING & EVENTS ---
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get the underlying PhpSpreadsheet worksheet
                $sheet = $event->sheet->getDelegate();

                // --- A. APPLY PRINT SETTINGS ---
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageSetup()->setHorizontalCentered(true);

                // --- B. ROWS & TITLE ---
                // Insert 8 rows. Headers move from Row 1 -> Row 9.
                $event->sheet->insertNewRowBefore(1, 8);

                // Main Title (Merged up to column K now)
                $sheet->mergeCells('C4:K4');
                $sheet->setCellValue('C4', 'IT DEPARTMENT TASK REPORT');
                $sheet->getStyle('C4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 26, 'color' => ['rgb' => '5d4085']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // --- C. HEADER STYLE ---
                $headerRow = 9;

                // Styled up to column K
                $sheet->getStyle('A'.$headerRow.':K'.$headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '5d4085']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(30);

                // --- D. DATA ALIGNMENT ---
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A'.$headerRow.':A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // Center Dates, Times, and Status (Columns H, I, J, K)
                $sheet->getStyle('H'.$headerRow.':K'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // --- E. STATUS COLORS ---
                for ($row = $headerRow + 1; $row <= $lastRow; $row++) {
                    // Status is now in column K (11th column)
                    $statusCell = 'K' . $row;
                    $status = $sheet->getCell($statusCell)->getValue();

                    $fontColor = '000000';
                    $fillColor = 'FFFFFF';

                    if (strcasecmp($status, 'Completed') == 0) {
                        $fontColor = '006100'; // Dark Green
                        $fillColor = 'C6EFCE'; // Light Green
                    } elseif (strcasecmp($status, 'In Progress') == 0) {
                        $fontColor = '9C5700'; // Dark Gold
                        $fillColor = 'FFEB9C'; // Light Yellow
                    } elseif (strcasecmp($status, 'Incomplete') == 0) {
                        $fontColor = '9C0006'; // Dark Red
                        $fillColor = 'FFC7CE'; // Light Red
                    }

                    $sheet->getStyle($statusCell)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => $fontColor]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
                    ]);
                }

                // --- F. BORDERS ---
                // Borders up to column K
                $sheet->getStyle('A'.$headerRow.':K'.$lastRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
                ]);

                // --- G. FOOTER SIGNATURES ---
                // Leaving a clean gap of 5 rows before signatures
                $sigRow = $lastRow + 5;

                $signatures = [
                    'A' => 'IT MANAGER',
                    'D' => 'TECHNICAL MANAGER',
                    'G' => 'MALL MANAGER',
                    'K' => 'DGM', // Shifted to K to align with the new right edge
                ];

                foreach ($signatures as $col => $title) {
                    $sheet->setCellValue($col . $sigRow, $title);
                    $sheet->getStyle($col . $sigRow)->applyFromArray([
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]
                        ],
                        'font' => ['bold' => true, 'size' => 11],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                    ]);
                }
            },
        ];
    }
}
