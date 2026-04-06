@extends('app')

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <h3 style="margin-top: 0; margin-bottom: 20px;">
                <i class="fa fa-pencil-square-o"></i> Edit Task
                <small>#{{ $task->task_id }}</small>
            </h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Update Task Information</h3>
                </div>

                <div class="panel-body">
                    <form action="{{ route('tasks.update', $task->task_id) }}" method="POST" class="form-horizontal" autocomplete="off">
                        @csrf
                        @method('PUT')

                        {{-- 1. DEPARTMENT --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Department <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" id="dept_id" name="dept_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->dept_id }}"
                                            {{ old('dept_id', $task->dept_id) == $dept->dept_id ? 'selected' : '' }}>
                                            {{ $dept->dept_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- 2. TASK TITLE (Smart Edit Logic) --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Task Title <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                {{-- Dropdown --}}
                                <select class="form-control" id="title_select" name="t_title">
                                    {{-- Populated via JS --}}
                                </select>

                                {{-- Manual Input --}}
                                <div class="input-group" id="manual_title_wrapper" style="display: none;">
                                    <input type="text" class="form-control" id="title_input" value="{{ $task->t_title }}" placeholder="Type custom task title...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-danger" type="button" id="cancel_manual_btn" title="Back to dropdown">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
{{-- LOCATION FIELD (EDIT) --}}
<div class="form-group">
    <label class="col-sm-3 control-label">Location</label>
    <div class="col-sm-9">
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
            <input type="text" class="form-control" name="location"
                   value="{{ old('location', $task->location) }}"
                   placeholder="e.g. Server Room, Main Hall...">
        </div>
    </div>
</div>
                        {{-- 3. ASSIGN TO --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Assign To <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" id="t_user_id" name="t_user_id" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->user_id }}"
                                                data-dept-id="{{ $emp->dept_id }}"
                                                {{ old('t_user_id', $task->t_user_id) == $emp->user_id ? 'selected' : '' }}>
                                            {{ $emp->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- 4. START TIME (Convenience Buttons) --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Start Time <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                    <input type="text" class="form-control" id="start_picker" name="t_start_time"
                                           value="{{ old('t_start_time', $task->t_start_time) }}" required>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" onclick="setStartNow()">Now</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- 5. END TIME (Convenience Buttons) --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Due Date <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-flag"></i></span>
                                    <input type="text" class="form-control" id="end_picker" name="t_end_time"
                                           value="{{ old('t_end_time', $task->t_end_time) }}" required>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" onclick="setDueEOD()">End of Day</button>
                                        <button class="btn btn-default" type="button" onclick="setDueTomorrow()">Tomorrow</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- 6. STATUS --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status" required>
                                    <option value="0" {{ $task->status == 0 ? 'selected' : '' }}>Incomplete</option>
                                    <option value="1" {{ $task->status == 1 ? 'selected' : '' }}>In Progress</option>
                                    <option value="2" {{ $task->status == 2 ? 'selected' : '' }}>Complete</option>
                                </select>
                            </div>
                        </div>

                        {{-- 7. DESCRIPTION --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="t_description" rows="3">{{ old('t_description', $task->t_description) }}</textarea>
                            </div>
                        </div>

                        {{-- BUTTONS --}}
                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3">
                                <button type="submit" class="btn btn-primary">Update Task</button>
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
    // Pass PHP Data
    const allTaskTypes = @json($taskTypes);
    const currentTitle = @json($task->t_title);

    let fpStart, fpEnd;

    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. DYNAMIC TITLE LOGIC (EDIT MODE) ---
        const deptSelect = document.getElementById('dept_id');
        const titleSelect = document.getElementById('title_select');
        const titleInput = document.getElementById('title_input');
        const manualWrapper = document.getElementById('manual_title_wrapper');
        const cancelBtn = document.getElementById('cancel_manual_btn');

        function updateTitleOptions(preselectValue = null) {
            const selectedDeptId = deptSelect.value;
            titleSelect.innerHTML = '';

            // Default Option
            const defaultOpt = document.createElement('option');
            defaultOpt.value = "";
            defaultOpt.textContent = "-- Select Task Title --";
            titleSelect.appendChild(defaultOpt);

            let valueFoundInList = false;

            if (selectedDeptId) {
                const filtered = allTaskTypes.filter(t => t.dept_id == selectedDeptId);

                filtered.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.task_name;
                    opt.textContent = t.task_name;
                    titleSelect.appendChild(opt);

                    if (preselectValue && t.task_name === preselectValue) {
                        opt.selected = true;
                        valueFoundInList = true;
                    }
                });

                // Manual Option
                const manualOpt = document.createElement('option');
                manualOpt.value = "MANUAL_ENTRY_TRIGGER";
                manualOpt.textContent = "Other / Enter Manually...";
                manualOpt.style.fontWeight = "bold";
                manualOpt.style.color = "#d9534f";
                titleSelect.appendChild(manualOpt);
            }

            return valueFoundInList;
        }

        // Logic to switch to manual input
        function switchToManual() {
            titleSelect.style.display = 'none';
            manualWrapper.style.display = 'table';
            titleSelect.removeAttribute('name');
            titleInput.setAttribute('name', 't_title');
        }

        // Logic to switch back to dropdown
        function switchToDropdown() {
            manualWrapper.style.display = 'none';
            titleSelect.style.display = 'block';
            titleInput.removeAttribute('name');
            titleSelect.setAttribute('name', 't_title');
        }

        // --- Event Listeners ---
        titleSelect.addEventListener('change', function() {
            if (this.value === "MANUAL_ENTRY_TRIGGER") {
                switchToManual();
                titleInput.focus();
                titleInput.value = '';
            }
        });

        cancelBtn.addEventListener('click', function() {
            switchToDropdown();
            titleSelect.selectedIndex = 0;
        });

        deptSelect.addEventListener('change', function() {
            updateTitleOptions();
            switchToDropdown(); // Reset to dropdown if dept changes
        });

        // --- INITIALIZATION FOR EDIT PAGE ---
        // 1. Populate options based on Dept
        const found = updateTitleOptions(currentTitle);

        // 2. If the current title wasn't found in the list (it's custom), switch to manual
        if (!found && currentTitle) {
            switchToManual();
        }

        // --- 2. ASSIGN TO FILTERING ---
        const empSelect = document.getElementById('t_user_id');
        const allEmployees = Array.from(empSelect.options).map(opt => opt.cloneNode(true));

        function filterEmployees() {
            const selectedDeptId = deptSelect.value;
            const currentVal = empSelect.value;
            empSelect.innerHTML = '';

            allEmployees.forEach(opt => {
                if (opt.value === "" || opt.getAttribute('data-dept-id') == selectedDeptId) {
                    empSelect.appendChild(opt);
                }
            });
            empSelect.value = currentVal; // Restore selection
        }
        deptSelect.addEventListener('change', filterEmployees);
        // filterEmployees(); // Optional: Run on load if needed, but HTML 'selected' handles it usually

        // --- 3. DATEPICKERS (AM/PM + 15min) ---
        fpStart = flatpickr("#start_picker", {
            enableTime: true,
            dateFormat: "Y-m-d h:i K",
            time_24hr: false,
            minuteIncrement: 15
        });

        fpEnd = flatpickr("#end_picker", {
            enableTime: true,
            dateFormat: "Y-m-d h:i K",
            time_24hr: false,
            minuteIncrement: 15
        });
    });

    // --- QUICK BUTTON FUNCTIONS ---
    function setStartNow() { if(fpStart) fpStart.setDate(new Date()); }
    function setDueEOD() {
        if(fpEnd) {
            const d = new Date(); d.setHours(18,0,0,0); fpEnd.setDate(d);
        }
    }
    function setDueTomorrow() {
        if(fpEnd) {
            const d = new Date(); d.setDate(d.getDate()+1); d.setHours(13,0,0,0); fpEnd.setDate(d);
        }
    }
</script>

@endsection
