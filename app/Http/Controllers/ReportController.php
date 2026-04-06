<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Admin;
use App\Exports\TaskReportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct()
    {
        // Any authenticated user can open the report page.
        // Data access (admin vs employee) is enforced inside index() and query building.
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();
        // Determine role: user_role 1 is Admin, 2 is Employee
        $isAdmin = $authUser && ($authUser->user_role == 1);

        // Handle ID retrieval safely (supports 'id' or 'user_id')
        $authId  = $authUser ? ($authUser->user_id ?? $authUser->id) : null;

        // 1) Defaults (same UI for both roles)
        $defaults = [
            'start_date'      => Carbon::now()->startOfWeek()->format('Y-m-d'),
            'report_type'     => 'weekly',
            'grouping'        => 'employee',
            'employee_id'     => 'all',
            'dept_id'         => 'all',
            'status_filter'   => 'all',
            'task_title'      => null,
            'dept_stat_filter'=> 'all_time'
        ];

        // 2) Merge request inputs
        $filters = array_merge($defaults, $request->all());

        // 3) SECURITY: If employee, force filters so they can only view their own data
        if (!$isAdmin) {
            $filters['employee_id'] = $authId;                  // Filter to current user
            $filters['dept_id']     = $authUser->dept_id;       // Keep department consistent
        }

        // 4) End date calculation
        $filters['end_date'] = $this->calculateEndDate($filters['report_type'], $filters['start_date'], $request->input('end_date'));

        // 5) CSV export (Logic respects the employee restriction passed via $filters)
        if ($request->has('export_section_csv') && $request->has('section_group_key')) {
            return $this->exportSectionCsv($filters, $isAdmin);
        }

        // 6) Build report + stats + dropdown data
        $reportData = $this->generateReportData($filters, $isAdmin);

        // For employees: department stats will also be restricted to their own tasks
        $deptStats = $this->getDepartmentStats($filters['dept_stat_filter'], $isAdmin);

        // Dropdown Data
        $departments = Department::orderBy('dept_name')->get();
        $employees   = Admin::where('user_role', 2)->orderBy('fullname')->get();

        // Optimize: Use distinct on DB table to avoid hydrating all Task models
        $taskTitles = DB::table('task_info')->distinct()->orderBy('t_title')->pluck('t_title');

        return view('admin.reports.index', compact(
            'filters',
            'reportData',
            'deptStats',
            'departments',
            'employees',
            'taskTitles',
            'isAdmin'
        ));
    }

    private function calculateEndDate($type, $startDate, $manualEndDate = null)
    {
        $start = Carbon::parse($startDate);

        switch ($type) {
            case 'weekly':   return $start->copy()->addDays(6)->format('Y-m-d');
            case 'biweekly': return $start->copy()->addDays(13)->format('Y-m-d');
            case 'monthly':  return $start->copy()->addMonth()->subDay()->format('Y-m-d');
            case 'all_time': return Carbon::now()->addYears(10)->format('Y-m-d');
            case 'custom':   return $manualEndDate ?: $start->format('Y-m-d');
            default:         return $manualEndDate ?? Carbon::now()->endOfWeek()->format('Y-m-d');
        }
    }

    /**
     * MAIN REPORT QUERY
     */
    private function generateReportData($filters, bool $isAdmin = false)
    {
        $authUser = Auth::user();
        $authId   = $authUser ? ($authUser->user_id ?? $authUser->id) : 0;

        // Use DB::table for performance on reports
        $query = DB::table('task_info as t')
            ->join('tbl_admin as a', 't.t_user_id', '=', 'a.user_id')
            ->leftJoin('tbl_admin as ab', 't.assigned_by', '=', 'ab.user_id')
            ->leftJoin('departments as d', 'a.dept_id', '=', 'd.dept_id')
            ->select(
                't.*',
                'a.fullname as assigned_to_name',
                'ab.fullname as assigned_by_name',
                'd.dept_name as employee_dept_name'
            );

        // Date range
        if ($filters['report_type'] !== 'all_time') {
            $query->whereDate('t.t_start_time', '>=', $filters['start_date'])
                  ->whereDate('t.t_start_time', '<=', $filters['end_date']);
        }

        if ($isAdmin) {
            // Admin filters
            if ($filters['employee_id'] !== 'all') {
                $query->where('t.t_user_id', $filters['employee_id']);
            }
            if ($filters['dept_id'] !== 'all') {
                $query->where('a.dept_id', $filters['dept_id']);
            }
        } else {
            // Employee Security: ONLY their tasks
            $query->where('t.t_user_id', $authId);
        }

        // Common filters
        if ($filters['status_filter'] !== 'all') {
            $query->where('t.status', $filters['status_filter']);
        }

        if (!empty($filters['task_title'])) {
            $query->where('t.t_title', $filters['task_title']);
        }

        $query->orderBy('t.t_start_time', 'DESC');

        $tasks = $query->get();

        // Calculate Summary
        $summary = [
            'total'       => $tasks->count(),
            'completed'   => $tasks->where('status', 2)->count(),
            'in_progress' => $tasks->where('status', 1)->count(),
            'incomplete'  => $tasks->where('status', 0)->count(),
        ];

        // Group Results
        $grouped = $tasks->groupBy(function ($item) use ($filters) {
            switch ($filters['grouping']) {
                case 'employee':   return $item->assigned_to_name ?? 'Unknown';
                case 'task_type':  return $item->t_title ?? 'Untitled';
                case 'day':        return Carbon::parse($item->t_start_time)->format('Y-m-d (l)');
                default:           return 'All Tasks';
            }
        });

        $processedGroups = [];
        foreach ($grouped as $key => $groupTasks) {
            $total = $groupTasks->count();
            $comp  = $groupTasks->where('status', 2)->count();

            $processedGroups[$key] = [
                'tasks'           => $groupTasks,
                'total'           => $total,
                'completed'       => $comp,
                'in_progress'     => $groupTasks->where('status', 1)->count(),
                'incomplete'      => $groupTasks->where('status', 0)->count(),
                'completion_rate' => $total > 0 ? round(($comp / $total) * 100, 2) : 0
            ];
        }

        return ['summary' => $summary, 'groups' => $processedGroups];
    }

private function exportSectionCsv($filters, bool $isAdmin = false)
    {
        // 1. Get Data
        $reportData = $this->generateReportData($filters, $isAdmin);
        $sectionKey = request('section_group_key');

        if (!isset($reportData['groups'][$sectionKey])) {
            return back()->with('error', 'Section data not found for export.');
        }

        $tasks = $reportData['groups'][$sectionKey]['tasks'];

        // 2. Create Filename (.xlsx for Excel)
        $safeKey = preg_replace('/[^a-z0-9]/i', '-', strtolower($sectionKey));
        $filename = 'report_' . $safeKey . '_' . date('Y-m-d') . '.xlsx';

        // 3. Download
        return Excel::download(new TaskReportExport($tasks), $filename);
    }

    private function getDepartmentStats($period, bool $isAdmin = false)
    {
        $start = null;
        $end   = null;
        $now   = Carbon::now();

        // Define date ranges
        switch ($period) {
            case 'today':      $start = $end = $now->format('Y-m-d'); break;
            case 'yesterday':  $start = $end = $now->subDay()->format('Y-m-d'); break;
            case 'this_week':  $start = $now->startOfWeek()->format('Y-m-d'); $end = $now->endOfWeek()->format('Y-m-d'); break;
            case 'this_month': $start = $now->startOfMonth()->format('Y-m-d'); $end = $now->endOfMonth()->format('Y-m-d'); break;
            case 'last_week':  $start = $now->subWeek()->startOfWeek()->format('Y-m-d'); $end = $now->subWeek()->endOfWeek()->format('Y-m-d'); break;
            case 'last_month': $start = $now->subMonth()->startOfMonth()->format('Y-m-d'); $end = $now->subMonth()->endOfMonth()->format('Y-m-d'); break;
        }

        $authUser = Auth::user();
        $authId   = $authUser ? ($authUser->user_id ?? $authUser->id) : 0;

        $query = DB::table('task_info as t')
            ->join('tbl_admin as a', 't.t_user_id', '=', 'a.user_id')
            ->join('departments as d', 'a.dept_id', '=', 'd.dept_id')
            ->select('d.dept_name', 't.status', DB::raw('count(t.task_id) as count'));

        if ($period !== 'all_time' && $start) {
            $query->whereDate('t.t_start_time', '>=', $start)
                  ->whereDate('t.t_start_time', '<=', $end);
        }

        if (!$isAdmin) {
            $query->where('t.t_user_id', $authId);
        }

        $raw = $query->groupBy('d.dept_name', 't.status')->get();

        $stats = [];
        foreach ($raw as $row) {
            $dept = $row->dept_name;
            if (!isset($stats[$dept])) {
                $stats[$dept] = ['Total' => 0, 'Completed' => 0, 'In Progress' => 0, 'Incomplete' => 0];
            }

            $count = $row->count;
            $stats[$dept]['Total'] += $count;

            if ($row->status == 2)      $stats[$dept]['Completed'] += $count;
            elseif ($row->status == 1) $stats[$dept]['In Progress'] += $count;
            elseif ($row->status == 0) $stats[$dept]['Incomplete'] += $count;
        }

        return $stats;
    }
}
