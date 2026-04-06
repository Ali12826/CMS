@extends('app')

{{--
    =============================================
    HEAD / FAVICON & STYLES
    =============================================
--}}
@section('head')
    <link rel="icon" type="image/png" href="{{ asset('storage/images/logo12.png') }}?v={{ time() }}">

    <style>
        :root {
            --giga-blue: #003366;
            --giga-gold: #FFD700;
        }

        /* --- BETTER PAGINATION STYLE --- */
        .pagination-wrapper { margin-top: 30px; margin-bottom: 20px; text-align: center; }
        .pagination { display: inline-flex !important; justify-content: center; align-items: center; gap: 5px; margin: 0; padding: 0; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 10px 15px; background: #fff; border-radius: 50px; }
        .pagination > li { display: inline-block; margin: 0; }
        .pagination > li > a, .pagination > li > span { color: var(--giga-blue); background-color: #fff; border: 1px solid #eee; padding: 8px 16px; border-radius: 30px !important; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; margin: 0 2px; }
        .pagination > li > a:hover { background-color: var(--giga-blue) !important; color: #fff !important; border-color: var(--giga-blue) !important; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,51,102, 0.3); }
        .pagination > .active > span, .pagination > .active > span:hover, .pagination > .active > a, .pagination > .active > a:hover { background-color: var(--giga-gold) !important; border-color: var(--giga-gold) !important; color: #000 !important; box-shadow: 0 2px 10px rgba(255, 215, 0, 0.4); transform: scale(1.05); z-index: 2; }
        .pagination > .disabled > span, .pagination > .disabled > span:hover, .pagination > .disabled > a, .pagination > .disabled > a:hover { background-color: #f8f9fa !important; color: #ccc !important; border-color: #f0f0f0 !important; cursor: not-allowed; transform: none; box-shadow: none; }
    </style>
@endsection

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" style="margin-bottom: 20px; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,0.05); border-top: 3px solid #3c8dbc;">
                <div class="panel-body" style="padding: 15px 20px;">
                    <div class="row">
                        <div class="col-sm-6" style="display: flex; align-items: center;">
                            <h3 style="margin: 0; font-weight: 600; color: #444; line-height: 1.2;">
                                <i class="fa fa-tasks" style="color: #3c8dbc; margin-right: 5px;"></i> Complaint Management System
                            </h3>
                        </div>
                        <div class="col-sm-6 text-right">
                            @auth
                                <div style="display: inline-block; vertical-align: middle;">
                                    <span class="text-muted" style="margin-right: 15px; font-size: 13px;">
                                        <i class="fa fa-user-circle"></i> Welcome, <strong style="color: #333;">{{ Auth::user()->fullname ?? Auth::user()->name }}</strong>
                                    </span>
                                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" style="border-radius: 3px; font-weight: 600;">
                                            <i class="fa fa-power-off"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-sign-in"></i> Log in
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS PANELS --}}
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="panel panel-primary">
                <div class="panel-heading text-center">
                    <div style="font-size: 30px; font-weight: bold;">{{ $totalTasks }}</div>
                    <div style="font-size: 14px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px;">
                        {{ $isAdmin ? 'Total Tasks' : 'My Tasks' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="panel panel-success">
                <div class="panel-heading text-center">
                    <div style="font-size: 30px; font-weight: bold;">{{ $completed }}</div>
                    <div style="font-size: 14px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px;">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="panel panel-warning">
                <div class="panel-heading text-center">
                    <div style="font-size: 30px; font-weight: bold;">{{ $inProgress }}</div>
                    <div style="font-size: 14px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px;">In Progress</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="panel panel-danger">
                <div class="panel-heading text-center">
                    <div style="font-size: 30px; font-weight: bold;">{{ $incomplete }}</div>
                    <div style="font-size: 14px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px;">Incomplete</div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="panel panel-default">
        <div class="panel-body" style="background-color: #f9f9f9;">
            <form method="GET" action="{{ route('tasks.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-2 col-sm-6">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="small text-muted" style="margin-bottom:2px;">View Mode</label>
                            <select name="assign_filter" class="form-control" onchange="this.form.submit()">
                                <option value="to_me" {{ $assignFilter == 'to_me' ? 'selected' : '' }}>Tasks Assigned To Me</option>
                                <option value="by_me" {{ $assignFilter == 'by_me' ? 'selected' : '' }}>Tasks Assigned By Me</option>
                                @if($isAdmin)
                                <option value="all" {{ $assignFilter == 'all' ? 'selected' : '' }}>All Tasks (Admin)</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="small text-muted" style="margin-bottom:2px;">Search</label>
                            <input type="text" name="q" class="form-control" placeholder="Title..." value="{{ request('q') }}" onblur="this.form.submit()">
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="small text-muted" style="margin-bottom:2px;">Department</label>
                            @php $selectedDept = request('dept_id', Auth::user()->dept_id); @endphp
                            <select name="dept_id" id="dept_id" class="form-control" onchange="this.form.submit()">
                                <option value="all" {{ $selectedDept == 'all' ? 'selected' : '' }}>All Depts</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->dept_id }}" {{ $selectedDept == $dept->dept_id ? 'selected' : '' }}>
                                        {{ $dept->dept_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="small text-muted" style="margin-bottom:2px;">Employee</label>
                            <select name="employee_id" id="employee_id" class="form-control" onchange="this.form.submit()">
                                <option value="all">All Employees</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->user_id }}" data-dept-id="{{ $emp->dept_id }}" {{ request('employee_id') == $emp->user_id ? 'selected' : '' }}>
                                        {{ $emp->fullname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                     <div class="col-md-2 col-sm-6">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="small text-muted" style="margin-bottom:2px;">Status</label>
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="all">All Status</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Incomplete</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>In Progress</option>
                                <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="row">
        {{-- LEFT COLUMN: Task List --}}
        <div class="col-md-8">
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-6 col-xs-6">
                    <h4 style="margin: 0; color: #555;">Tasks List</h4>
                </div>
                <div class="col-md-6 col-xs-6 text-right">
                    <a href="{{ route('tasks.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> New Task
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if($tasks->isEmpty())
                <div class="alert alert-info">No tasks found matching your criteria.</div>
            @else
                @foreach($tasks as $task)
                    @php
                        $borderColor = '#d9534f';
                        $statusLabel = 'label-danger';
                        $statusText = 'Incomplete';
                        if($task->status == 1) {
                            $borderColor = '#f0ad4e';
                            $statusLabel = 'label-warning';
                            $statusText = 'In Progress';
                        } elseif($task->status == 2) {
                            $borderColor = '#5cb85c';
                            $statusLabel = 'label-success';
                            $statusText = 'Completed';
                        }
                    @endphp

                    <div class="panel panel-default" style="border-left: 5px solid {{ $borderColor }}; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4 style="margin-top: 0; margin-bottom: 5px; font-weight: 600;">
                                        {{ $task->t_title }}
                                        <span class="label {{ $statusLabel }}" style="font-size: 10px; vertical-align: middle; margin-left: 10px;">
                                            {{ $statusText }}
                                        </span>
                                    </h4>
                                    <p class="text-muted small" style="margin-bottom: 10px;">
                                        <i class="fa fa-building"></i> {{ $task->department->dept_name ?? 'N/A' }} &nbsp;|&nbsp;
                                        <strong>To:</strong> {{ $task->assignedTo->fullname ?? 'N/A' }} &nbsp;|&nbsp;
                                        <strong>By:</strong> {{ $task->assignedBy->fullname ?? 'N/A' }}
                                    </p>

                                    {{-- Timezone Fixed Here --}}
                                    <small style="color: #777;">
                                        <i class="fa fa-clock-o"></i> Start: {{ \Carbon\Carbon::parse($task->t_start_time)->setTimezone('Asia/Karachi')->format('M d, h:i A') }} <br>
                                        <i class="fa fa-flag"></i> Due: {{ \Carbon\Carbon::parse($task->t_end_time)->setTimezone('Asia/Karachi')->format('M d, h:i A') }}
                                    </small>
                                </div>

                                <div class="col-md-4 text-right" style="padding-top: 20px;">
                                    <a href="{{ route('tasks.show', $task->task_id) }}" class="btn btn-default btn-sm" title="View Details"><i class="fa fa-eye"></i> View</a>
                                    <a href="{{ route('tasks.edit', $task->task_id) }}" class="btn btn-primary btn-sm" title="Edit Task"><i class="fa fa-pencil"></i> Edit</a>
                                    <form action="{{ route('tasks.destroy', $task->task_id) }}" method="POST" style="display: inline-block;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete Permanently" onclick="return confirm('Are you sure you want to PERMANENTLY delete this task? This action cannot be undone.');"><i class="fa fa-trash"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="pagination-wrapper">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>

       {{-- RIGHT COLUMN: Employee Status --}}
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-users"></i> Department Status</h3>
            </div>
            <ul class="list-group" style="max-height: 600px; overflow-y: auto;">
                @foreach($deptEmployees as $emp)
                    <li class="list-group-item">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h5 style="margin: 0; font-size: 14px; font-weight: 600;">
                                {{ $emp->fullname }}
                            </h5>

                            {{-- NEW: Role Badge
                            @if($emp->user_role == 1)
                                <span class="label label-danger" style="font-size: 10px;">Admin</span>
                            @else
                                <span class="label label-default" style="font-size: 10px;">User</span>
                            @endif
                        </div>

                        <small class="text-muted">{{ $emp->dept_name }}</small> --}}

                        <div style="margin-top: 5px;">
                            @if($emp->is_busy)
                                <span class="text-warning small">
                                    <i class="fa fa-spinner fa-spin"></i> Working on:
                                </span>
                                <small style="display:block; color:#555;">
                                    {{ \Illuminate\Support\Str::limit($emp->current_task, 30) }}
                                </small>
                            @else
                                <span class="text-success small">
                                    <i class="fa fa-check-circle"></i> Available
                                </span>
                            @endif
                        </div>
                    </li>
                @endforeach

                @if($deptEmployees->isEmpty())
                    <li class="list-group-item text-muted text-center">
                        No members found in your department.
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div> {{-- End Row --}}
</div> {{-- End Container --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deptSelect = document.getElementById('dept_id');
        const empSelect = document.getElementById('employee_id');

        // Safety check in case elements don't exist on this specific page
        if (!deptSelect || !empSelect) return;

        const allEmployees = Array.from(empSelect.options).map(opt => opt.cloneNode(true));

        function filterEmployees() {
            const selectedDeptId = deptSelect.value;
            const currentEmpValue = empSelect.value;
            empSelect.innerHTML = '';

            allEmployees.forEach(function(option) {
                if (option.value === "all") {
                    empSelect.appendChild(option);
                    return;
                }
                const employeeDeptId = option.getAttribute('data-dept-id');
                // Allow logic to show everyone if needed, or filter by dept
                if (selectedDeptId === 'all' || !selectedDeptId) {
                    empSelect.appendChild(option);
                } else if (employeeDeptId == selectedDeptId) {
                    empSelect.appendChild(option);
                }
            });

            // Try to restore previous selection
            empSelect.value = currentEmpValue;
            // If selection is invalid/hidden, default to 'all' or first option
            if (empSelect.selectedIndex === -1) {
                empSelect.value = "all";
                if(empSelect.selectedIndex === -1 && empSelect.options.length > 0) {
                    empSelect.selectedIndex = 0;
                }
            }
        }

        deptSelect.addEventListener('change', filterEmployees);
        // Initial run
        filterEmployees();
    });
</script>
@endsection
