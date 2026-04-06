@extends('app')

@section('content')
<div class="container-fluid">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        :root { --giga-blue:#003366; --giga-gold:#b38800; --giga-gold-light:#e0b81c; --export-gold-dark:#8c6a00; --export-gold-light:#ffd700; }
        h1,h2,h3,h4,h5,h6,.panel-heading{font-weight:700;}
        .well-custom{background:#fff;border:none;padding:30px 20px;border-radius:8px;box-shadow:0 10px 25px rgba(0,0,0,.1),0 3px 5px rgba(0,0,0,.05);}
        .report-header{border-bottom:3px solid var(--giga-gold);margin-bottom:20px;}
        .report-header h4{color:var(--giga-blue);}
        .panel-primary,.panel-info{border:1px solid #e7e7e7;}
        .panel-info>.panel-heading,.panel-primary>.panel-heading{color:#fff!important;background:linear-gradient(180deg,#004488 0%, var(--giga-blue) 100%)!important;border-color:var(--giga-blue)!important;border-radius:5px 5px 0 0;}
        .btn-primary{background-color:var(--giga-blue)!important;border-color:var(--giga-blue)!important;box-shadow:0 2px 5px rgba(0,51,102,.4);}
        .btn-primary:hover{background-color:#002244!important;}
        .action-btn.section.csv{background-color:var(--export-gold-light);color:var(--giga-blue);border:1px solid var(--export-gold-dark);font-weight:700;text-shadow:0 1px 0 rgba(255,255,255,.5);box-shadow:0 2px 5px rgba(0,0,0,.3);padding:7px 12px;margin-left:10px;border-radius:4px;display:inline-block;cursor:pointer;}
        .action-btn.section.csv:hover{background-color:var(--giga-gold);color:#fff;text-decoration:none;}
        .compact-task-card{animation:fadeInUp .5s ease-out forwards;opacity:0;}
        .compact-task-card .panel-primary{border:none;border-radius:8px;overflow:hidden;box-shadow:0 8px 15px rgba(0,0,0,.15),0 0 0 1px rgba(179,136,0,.7);transition:transform .3s ease;}
        .compact-task-card .panel-primary:hover{transform:translateY(-3px);}
        .compact-task-card .panel-heading{border-bottom:2px solid var(--giga-gold);min-height:70px;}
        .compact-task-card .huge{font-size:30px;line-height:1;color:var(--giga-gold-light);}
        .dept-stat-info{display:flex;justify-content:space-between;align-items:center;font-size:12px;margin-bottom:5px;border-bottom:1px solid #eee;padding-bottom:2px;}
        .dept-stat-info strong{color:var(--giga-blue);}
        .summary-card{opacity:0;animation:fadeInUp .5s ease-out forwards;}
        .summary-card .panel{box-shadow:0 5px 10px rgba(0,0,0,.1);border-radius:6px;}
        .task-report-table th{background-color:var(--giga-blue);color:#fff;border-color:#002244!important;}
        .task-report-table tr:hover{background-color:#e6e6fa;}
        .report-group .panel-heading{cursor:pointer;display:flex;justify-content:space-between;align-items:center;}

        /* Pagination Styles */
        .pagination-wrapper {
            margin: 20px 0;
            text-align: center;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .pagination-info {
            margin-bottom: 10px;
            color: #666;
            font-size: 14px;
        }
        .pagination-controls {
            display: inline-block;
        }
        .pagination-controls button {
            background-color: var(--giga-blue);
            color: white;
            border: none;
            padding: 8px 15px;
            margin: 0 5px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .pagination-controls button:hover:not(:disabled) {
            background-color: #002244;
        }
        .pagination-controls button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }
        .pagination-controls .page-numbers {
            display: inline-block;
            margin: 0 10px;
        }
        .pagination-controls .page-numbers button {
            min-width: 35px;
        }
        .pagination-controls .page-numbers button.active {
            background-color: var(--giga-gold);
            font-weight: bold;
        }
        .task-row {
            display: none;
        }
        .task-row.visible {
            display: table-row;
        }
    </style>

    <div class="row">
        <div class="col-md-12">
            <div class="well-custom">
                <h3 class="text-center">
                    <i class="fa fa-bar-chart"></i>
                    {{ (isset($isAdmin) && $isAdmin) ? 'Employee' : 'My' }} Performance Report
                </h3>
               {{--  timezone --}}
<i class="fa fa-clock-o"></i> {{ now()->setTimezone('Asia/Karachi')->format('l, M d, Y h:i:s A') }}
                <hr>

                {{-- 1) DEPARTMENT STATS (tiles) --}}
                <div class="panel panel-default">
                    <div class="panel-heading" style="background-color:#f5f5f5; color:#333; overflow:hidden;">
                        <span class="pull-left" style="font-weight:bold; margin-top:5px;">
                            {{ (isset($isAdmin) && $isAdmin) ? 'Department Overview' : 'My Department Stats' }}
                        </span>

                        <form action="{{ route('admin.reports.index') }}" method="GET" class="pull-right form-inline" style="margin:0;">
                            @foreach($filters as $key => $value)
                                @if($key != 'dept_stat_filter')
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach

                            <label><small>Period:</small></label>
                            <select name="dept_stat_filter" class="form-control input-sm" onchange="this.form.submit()">
                                <option value="today" {{ $filters['dept_stat_filter']=='today' ? 'selected' : '' }}>Today</option>
                                <option value="yesterday" {{ $filters['dept_stat_filter']=='yesterday' ? 'selected' : '' }}>Yesterday</option>
                                <option value="this_week" {{ $filters['dept_stat_filter']=='this_week' ? 'selected' : '' }}>This Week</option>
                                <option value="this_month" {{ $filters['dept_stat_filter']=='this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="all_time" {{ $filters['dept_stat_filter']=='all_time' ? 'selected' : '' }}>All Time</option>
                            </select>
                        </form>
                    </div>

                    <div class="panel-body" style="background:#f9f9f9;">
                        <div class="row">
                            @forelse($deptStats as $deptName => $stats)
                                <div class="col-lg-3 col-md-6 col-sm-6 compact-task-card" style="margin-bottom: 20px;">
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <i class="fa fa-building fa-3x" style="opacity:0.5;"></i>
                                                </div>
                                                <div class="col-xs-9 text-right">
                                                    <div class="huge">{{ $stats['Total'] }}</div>
                                                    <div>{{ $deptName }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer">
                                            <div class="dept-stat-info">
                                                <span>Completed</span>
                                                <strong>{{ $stats['Completed'] }}</strong>
                                            </div>
                                            <div class="dept-stat-info">
                                                <span>In Progress</span>
                                                <strong>{{ $stats['In Progress'] }}</strong>
                                            </div>
                                            <div class="dept-stat-info">
                                                <span>Incomplete</span>
                                                <strong>{{ $stats['Incomplete'] }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-md-12 text-center text-muted">No data found for this period.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <hr style="border-top: 2px dashed #ccc;">

                {{-- 2) REPORT FILTERS --}}
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="fa fa-filter"></i> Generate Detailed Report</div>
                    <div class="panel-body">
                        <form action="{{ route('admin.reports.index') }}" method="GET" autocomplete="off">
                            <input type="hidden" name="dept_stat_filter" value="{{ $filters['dept_stat_filter'] }}">

                            <div class="row">
                                <div class="col-md-3">
                                    <label>Report Type</label>
                                    <select name="report_type" class="form-control" onchange="toggleDateInputs(this.value)">
                                        <option value="weekly"   {{ $filters['report_type']=='weekly' ? 'selected' : '' }}>Weekly (7 Days)</option>
                                        <option value="biweekly" {{ $filters['report_type']=='biweekly' ? 'selected' : '' }}>Bi-Weekly (14 Days)</option>
                                        <option value="monthly"  {{ $filters['report_type']=='monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="all_time" {{ $filters['report_type']=='all_time' ? 'selected' : '' }}>All Time</option>
                                        <option value="custom"   {{ $filters['report_type']=='custom' ? 'selected' : '' }}>Custom Range</option>
                                    </select>
                                </div>
                                <div class="col-md-3" id="start-date-div">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
                                </div>
                                <div class="col-md-3" id="end-date-div" style="display: {{ $filters['report_type']=='custom' ? 'block' : 'none' }};">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
                                </div>
                                <div class="col-md-3">
                                    <label>Grouping</label>
                                    <select name="grouping" class="form-control">
                                        <option value="employee" {{ $filters['grouping']=='employee' ? 'selected' : '' }}>By Employee</option>
                                        <option value="task_type" {{ $filters['grouping']=='task_type' ? 'selected' : '' }}>By Task Title</option>
                                        <option value="day" {{ $filters['grouping']=='day' ? 'selected' : '' }}>By Day</option>
                                        <option value="none" {{ $filters['grouping']=='none' ? 'selected' : '' }}>No Grouping</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 15px;">
                                @if(isset($isAdmin) && $isAdmin)
                                    <div class="col-md-3">
                                        <label>Department</label>
                                        <select name="dept_id" class="form-control">
                                            <option value="all">All Departments</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->dept_id }}" {{ $filters['dept_id']==$dept->dept_id ? 'selected' : '' }}>
                                                    {{ $dept->dept_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Employee</label>
                                        <select name="employee_id" class="form-control">
                                            <option value="all">All Employees</option>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->user_id }}" {{ $filters['employee_id']==$emp->user_id ? 'selected' : '' }}>
                                                    {{ $emp->fullname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="col-md-3">
                                        <label>Department</label>
                                        <input type="text" class="form-control" value="My Department" disabled style="background-color:#eee;">
                                        <input type="hidden" name="dept_id" value="{{ auth()->user()->dept_id }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Employee</label>
                                        <input type="text" class="form-control" value="{{ auth()->user()->fullname }}" disabled style="background-color:#eee;">
                                        <input type="hidden" name="employee_id" value="{{ auth()->id() }}">
                                    </div>
                                @endif

                                <div class="col-md-3">
                                    <label>Status</label>
                                    <select name="status_filter" class="form-control">
                                        <option value="all">All Statuses</option>
                                        <option value="2" {{ $filters['status_filter']=='2' ? 'selected' : '' }}>Completed</option>
                                        <option value="1" {{ $filters['status_filter']=='1' ? 'selected' : '' }}>In Progress</option>
                                        <option value="0" {{ $filters['status_filter']=='0' ? 'selected' : '' }}>Incomplete</option>
                                    </select>
                                </div>

                                <div class="col-md-3" style="margin-top: 25px;">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-refresh"></i> Generate Report
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 3) REPORT RESULTS --}}
                @if(isset($reportData))
                    <div class="report-header">
                        <h4>Results: {{ $filters['start_date'] }} to {{ $filters['end_date'] }}</h4>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-6 summary-card">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3"><i class="fa fa-tasks fa-4x"></i></div>
                                        <div class="col-xs-9 text-right">
                                            <h3>{{ $reportData['summary']['total'] }}</h3>
                                            <div>Total Tasks</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 summary-card" style="animation-delay: 0.1s;">
                            <div class="panel panel-success">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3"><i class="fa fa-check-circle fa-4x"></i></div>
                                        <div class="col-xs-9 text-right">
                                            <h3>{{ $reportData['summary']['completed'] }}</h3>
                                            <div>Completed</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 summary-card" style="animation-delay: 0.2s;">
                            <div class="panel panel-warning">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3"><i class="fa fa-spinner fa-4x"></i></div>
                                        <div class="col-xs-9 text-right">
                                            <h3>{{ $reportData['summary']['in_progress'] }}</h3>
                                            <div>In Progress</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 summary-card" style="animation-delay: 0.3s;">
                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3"><i class="fa fa-times-circle fa-4x"></i></div>
                                        <div class="col-xs-9 text-right">
                                            <h3>{{ $reportData['summary']['incomplete'] }}</h3>
                                            <div>Incomplete</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach($reportData['groups'] as $groupKey => $group)
                        @php
                            $tableId = 'table-' . Str::slug($groupKey);
                        @endphp
                        <div class="panel panel-default report-group" style="margin-top: 20px;">
                            <div class="panel-heading" data-toggle="collapse" href="#collapse-{{ Str::slug($groupKey) }}">
                                <div class="panel-title-text">
                                    <h4 class="panel-title" style="margin-right: 15px; font-weight:700;">{{ $groupKey }}</h4>
                                    <h5 style="margin:0;">(Tasks: {{ $group['total'] }} | Completed: <strong>{{ $group['completion_rate'] }}%</strong>)</h5>
                                </div>

                                <form action="{{ route('admin.reports.index') }}" method="POST" target="_blank" onclick="event.stopPropagation();">
                                    @csrf
                                    @foreach($filters as $k => $v)
                                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                    @endforeach
                                    <input type="hidden" name="section_group_key" value="{{ $groupKey }}">
                                    <button type="submit" name="export_section_csv" value="1" class="action-btn section csv">
                                        <i class="fa fa-download"></i> Export CSV
                                    </button>
                                </form>
                            </div>

                            <div id="collapse-{{ Str::slug($groupKey) }}" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    {{-- Pagination Controls Top --}}
                                    <div class="pagination-wrapper" id="pagination-top-{{ $tableId }}"></div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped task-report-table" id="{{ $tableId }}">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Description</th>
                                                    <th>Assigned To</th>
                                                    <th>Start Time</th>
                                                    <th>End Time</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($group['tasks'] as $task)
                                                    <tr class="task-row">
                                                        <td>{{ $task->t_title }}</td>
                                                        <td class="description-cell">{{ Str::limit($task->t_description, 50) }}</td>
                                                        <td>{{ $task->assigned_to_name }}</td>
                                                        <td>{{ $task->t_start_time }}</td>
                                                        <td>{{ $task->t_end_time }}</td>
                                                        <td>
                                                            @if($task->status == 2)
                                                                <span class="label label-success">Completed</span>
                                                            @elseif($task->status == 1)
                                                                <span class="label label-warning">In Progress</span>
                                                            @else
                                                                <span class="label label-danger">Incomplete</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Pagination Controls Bottom --}}
                                    <div class="pagination-wrapper" id="pagination-bottom-{{ $tableId }}"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleDateInputs(val) {
    document.getElementById('end-date-div').style.display = (val === 'custom') ? 'block' : 'none';
}

// Simple Pagination Function
function initPagination(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const rows = table.querySelectorAll('tbody .task-row');
    const totalRows = rows.length;
    const rowsPerPage = 25;
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    let currentPage = 1;

    function showPage(page) {
        currentPage = page;

        // Hide all rows
        rows.forEach(row => row.classList.remove('visible'));

        // Show rows for current page
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        for (let i = start; i < end && i < totalRows; i++) {
            rows[i].classList.add('visible');
        }

        // Update pagination controls
        updatePaginationControls();
    }

    function updatePaginationControls() {
        const start = (currentPage - 1) * rowsPerPage + 1;
        const end = Math.min(currentPage * rowsPerPage, totalRows);

        const paginationHTML = `
            <div class="pagination-info">
                Showing ${start} to ${end} of ${totalRows} tasks
            </div>
            <div class="pagination-controls">
                <button onclick="goToPage('${tableId}', 1)" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="fa fa-angle-double-left"></i> First
                </button>
                <button onclick="goToPage('${tableId}', ${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="fa fa-angle-left"></i> Previous
                </button>

                <span class="page-numbers">
                    ${generatePageNumbers(currentPage, totalPages, tableId)}
                </span>

                <button onclick="goToPage('${tableId}', ${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                    Next <i class="fa fa-angle-right"></i>
                </button>
                <button onclick="goToPage('${tableId}', ${totalPages})" ${currentPage === totalPages ? 'disabled' : ''}>
                    Last <i class="fa fa-angle-double-right"></i>
                </button>
            </div>
        `;

        // Update both top and bottom pagination
        const topPagination = document.getElementById('pagination-top-' + tableId);
        const bottomPagination = document.getElementById('pagination-bottom-' + tableId);

        if (topPagination) topPagination.innerHTML = paginationHTML;
        if (bottomPagination) bottomPagination.innerHTML = paginationHTML;
    }

    function generatePageNumbers(current, total, tableId) {
        let html = '';
        let startPage = Math.max(1, current - 2);
        let endPage = Math.min(total, current + 2);

        // Adjust if we're near the start or end
        if (current <= 3) {
            endPage = Math.min(5, total);
        }
        if (current >= total - 2) {
            startPage = Math.max(1, total - 4);
        }

        // Add first page if not in range
        if (startPage > 1) {
            html += `<button onclick="goToPage('${tableId}', 1)">1</button>`;
            if (startPage > 2) {
                html += '<span style="padding: 0 5px;">...</span>';
            }
        }

        // Add page numbers
        for (let i = startPage; i <= endPage; i++) {
            html += `<button onclick="goToPage('${tableId}', ${i})" ${i === current ? 'class="active"' : ''}>${i}</button>`;
        }

        // Add last page if not in range
        if (endPage < total) {
            if (endPage < total - 1) {
                html += '<span style="padding: 0 5px;">...</span>';
            }
            html += `<button onclick="goToPage('${tableId}', ${total})">${total}</button>`;
        }

        return html;
    }

    // Store pagination function for this table
    window['pagination_' + tableId] = {
        showPage: showPage,
        currentPage: currentPage,
        totalPages: totalPages
    };

    // Show first page initially
    showPage(1);
}

// Global function to change page
function goToPage(tableId, page) {
    const pagination = window['pagination_' + tableId];
    if (pagination && page >= 1 && page <= pagination.totalPages) {
        pagination.showPage(page);
    }
}

// Initialize pagination for all tables when page loads
document.addEventListener('DOMContentLoaded', function() {
    const tables = document.querySelectorAll('.task-report-table');
    tables.forEach(function(table) {
        initPagination(table.id);
    });
});
</script>
@endsection
