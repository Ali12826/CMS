@extends('app')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <h3 style="margin-top: 0; margin-bottom: 20px;">
                <i class="fa fa-plus-circle"></i> Create New Task
            </h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title" style="line-height: 2;">Task Form</h3>
                </div>

                <div class="panel-body">
                    <form action="{{ route('tasks.store') }}" method="POST" class="form-horizontal" autocomplete="off">
                        @csrf

                        {{-- 1. DEPARTMENT --}}
                        <div class="form-group {{ $errors->has('dept_id') ? 'has-error' : '' }}">
                            <label for="dept_id" class="col-sm-3 control-label">Department <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" id="dept_id" name="dept_id" required>
                                    <option value="" selected>Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->dept_id }}" {{ old('dept_id') == $dept->dept_id ? 'selected' : '' }}>
                                            {{ $dept->dept_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- 2. TASK TITLE --}}
                        <div class="form-group {{ $errors->has('t_title') ? 'has-error' : '' }}">
                            <label class="col-sm-3 control-label">Task Title <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" id="title_select" name="t_title">
                                    <option value="">Select Department First</option>
                                </select>

                                <div class="input-group" id="manual_title_wrapper" style="display: none;">
                                    <input type="text" class="form-control" id="title_input" placeholder="Type custom task title...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-danger" type="button" id="cancel_manual_btn" title="Back to list">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </span>
                                </div>
                                @if ($errors->has('t_title'))
                                    <span class="help-block">{{ $errors->first('t_title') }}</span>
                                @endif
                            </div>
                        </div>
                        {{-- LOCATION FIELD --}}
<div class="form-group">
    <label class="col-sm-3 control-label">Location</label>
    <div class="col-sm-9">
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
            <input type="text" class="form-control" name="location" placeholder="e.g. Server Room, Main Hall, Site A...">
        </div>
    </div>
</div>

                        {{-- 3. ASSIGN TO --}}
                        <div class="form-group">
                            <label for="t_user_id" class="col-sm-3 control-label">Assign To <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" id="t_user_id" name="t_user_id" required>
                                    <option value="" selected>Select Employee</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->user_id }}" data-dept-id="{{ $emp->dept_id }}">
                                            {{ $emp->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{--
                            4. DATES (IMPROVED CONVENIENCE)
                            Added Quick-Select Buttons
                        --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Start Time <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                    <input type="text" class="form-control" id="start_picker" name="t_start_time" placeholder="Select Start Time" required>
                                    {{-- Quick Action Button --}}
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" onclick="setStartNow()" title="Set to Current Time">Now</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Due Date <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-flag"></i></span>
                                    <input type="text" class="form-control" id="end_picker" name="t_end_time" placeholder="Select Due Date" required>
                                    {{-- Quick Action Buttons --}}
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" onclick="setDueEOD()" title="End of Day (5:00 PM)">End of Day</button>
                                        <button class="btn btn-default" type="button" onclick="setDueTomorrow()" title="Tomorrow Morning">Tomorrow</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- 5. DESCRIPTION --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="t_description" rows="4"></textarea>
                            </div>
                        </div>

                        {{-- BUTTONS --}}
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-primary">Create Task</button>
                                <a href="{{ route('tasks.index') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const allTaskTypes = @json($taskTypes);

    // Global variables for Datepickers
    let fpStart, fpEnd;

    document.addEventListener('DOMContentLoaded', function() {
        // --- EXISTING LOGIC ---
        const deptSelect = document.getElementById('dept_id');
        const titleSelect = document.getElementById('title_select');
        const titleInput = document.getElementById('title_input');
        const manualWrapper = document.getElementById('manual_title_wrapper');
        const cancelBtn = document.getElementById('cancel_manual_btn');
        const empSelect = document.getElementById('t_user_id');
        const allEmployees = Array.from(empSelect.options).map(opt => opt.cloneNode(true));

        function updateTitleOptions() {
            const selectedDeptId = deptSelect.value;
            titleSelect.innerHTML = '';
            const defaultOpt = document.createElement('option');
            defaultOpt.value = "";
            defaultOpt.textContent = selectedDeptId ? "-- Select Task Title --" : "-- Select Department First --";
            titleSelect.appendChild(defaultOpt);
            if (selectedDeptId) {
                const filtered = allTaskTypes.filter(t => t.dept_id == selectedDeptId);
                filtered.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.task_name;
                    opt.textContent = t.task_name;
                    titleSelect.appendChild(opt);
                });
                const manualOpt = document.createElement('option');
                manualOpt.value = "MANUAL_ENTRY_TRIGGER";
                manualOpt.textContent = "Other / Enter Manually...";
                manualOpt.style.fontWeight = "bold";
                manualOpt.style.color = "#d9534f";
                titleSelect.appendChild(manualOpt);
            }
        }

        titleSelect.addEventListener('change', function() {
            if (this.value === "MANUAL_ENTRY_TRIGGER") {
                titleSelect.style.display = 'none';
                manualWrapper.style.display = 'table';
                titleSelect.removeAttribute('name');
                titleInput.setAttribute('name', 't_title');
                titleInput.focus();
                titleInput.value = '';
            }
        });

        cancelBtn.addEventListener('click', function() {
            manualWrapper.style.display = 'none';
            titleSelect.style.display = 'block';
            titleInput.removeAttribute('name');
            titleSelect.setAttribute('name', 't_title');
            titleSelect.selectedIndex = 0;
        });

        function filterEmployees() {
            const selectedDeptId = deptSelect.value;
            empSelect.innerHTML = '';
            allEmployees.forEach(opt => {
                if (opt.value === "" || opt.getAttribute('data-dept-id') == selectedDeptId) {
                    empSelect.appendChild(opt);
                }
            });
            if (empSelect.selectedIndex === -1) empSelect.selectedIndex = 0;
        }

        deptSelect.addEventListener('change', function() {
            updateTitleOptions();
            filterEmployees();
            if(manualWrapper.style.display !== 'none') cancelBtn.click();
        });

        if(deptSelect.value) {
            updateTitleOptions();
            filterEmployees();
        }

        // --- NEW CONVENIENT DATEPICKER LOGIC ---

        // 1. Initialize Start Time Picker
        fpStart = flatpickr("#start_picker", {
            enableTime: true,
            dateFormat: "Y-m-d h:i K",
            time_24hr: false,
            defaultDate: new Date(),
            minuteIncrement: 15, // Convenience: Snap to 00, 15, 30, 45
            onChange: function(selectedDates, dateStr, instance) {
                // Ensure End Date cannot be before Start Date
                fpEnd.set('minDate', dateStr);
            }
        });

        // 2. Initialize Due Date Picker
        fpEnd = flatpickr("#end_picker", {
            enableTime: true,
            dateFormat: "Y-m-d h:i K",
            time_24hr: false,
            minuteIncrement: 15,
            minDate: "today" // Cannot set due date in past
        });
    });

    // --- QUICK ACTION FUNCTIONS ---

    function setStartNow() {
        if(fpStart) {
            fpStart.setDate(new Date());
            // Also update the minimum allowed date for Due Date
            fpEnd.set('minDate', new Date());
        }
    }

    function setDueEOD() {
        if(fpEnd) {
            // Set to Today at 6:00 PM (18:00)
            const date = new Date();
            date.setHours(18, 0, 0, 0);
            fpEnd.setDate(date);
        }
    }

    function setDueTomorrow() {
        if(fpEnd) {
            // Set to Tomorrow at 12:00 AM
            const date = new Date();
            date.setDate(date.getDate() + 1);
            date.setHours(13, 0, 0, 0);
            fpEnd.setDate(date);
        }
    }
</script>
@endsection
