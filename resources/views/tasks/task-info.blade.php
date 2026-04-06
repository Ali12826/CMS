@extends('app')

@section('content')

{{-- External Libraries --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    /* === Custom Styles === */
    body { transition: background-color 0.3s; }
    .dark-mode body { background-color: #121212 !important; color: #e0e0e0; }

    /* Wrapper for full background coverage */
    .dark-mode .main-content { background-color: #121212; }

    .task-header {
        background: linear-gradient(135deg, #0f0096ff 0%, #00d9ffff 100%);
        color: white; padding: 30px; border-radius: 10px;
        margin: 20px 0 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .task-header h2 { margin-top: 10px; font-weight: 700; color: white;}
    .task-header p { color: rgba(255,255,255, 0.9); }

    .dark-mode .task-header {
        background: linear-gradient(135deg, #6200ee 0%, #bb86fc 100%) !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.5);
    }

    .dark-mode-toggle {
        position: fixed; top: 80px; right: 20px; background: white; color: #667eea;
        border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1); font-size: 18px; line-height: 40px;
        text-align: center; z-index: 1000; transition: all 0.3s;
    }
    .dark-mode-toggle:hover { background: #f0f0f0; transform: scale(1.05); }
    .dark-mode .dark-mode-toggle { background: #2c2c2c; color: #bb86fc; }
    .dark-mode .dark-mode-toggle:hover { background: #3d3d3d; }

    .details-card {
        background: #ffffff; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 30px; margin-bottom: 30px; transition: all 0.3s;
    }
    .dark-mode .details-card { background: #1e1e1e; box-shadow: 0 4px 15px rgba(0,0,0,0.4); }

    .details-card h3 {
        font-weight: 700; color: #333; border-bottom: 2px solid #667eea;
        padding-bottom: 15px; margin-bottom: 25px; display: inline-block;
    }
    .dark-mode .details-card h3 { color: #f0f0f0; border-bottom: 2px solid #bb86fc; }

    .form-group { margin-bottom: 20px; }
    .control-label {
        font-weight: 600; color: #333; margin-bottom: 8px; display: block;
    }
    .dark-mode .control-label { color: #e0e0e0; }

    .form-control, select, textarea {
        width: 100%; height: 40px; border-radius: 8px; border: 1px solid #ddd;
        box-shadow: none; transition: all 0.3s; padding: 5px 10px;
        background-color: #fff; color: #333;
    }
    textarea.form-control { height: auto; }

    .form-control:focus, select:focus, textarea:focus {
        border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); outline: none;
    }

    /* Readonly inputs specific style */
    .form-control[readonly] { background-color: #e9ecef; }

    .dark-mode .form-control, .dark-mode select, .dark-mode textarea {
        background-color: #2c2c2c; border: 1px solid #3d3d3d; color: #f0f0f0;
    }
    .dark-mode .form-control:focus, .dark-mode select:focus, .dark-mode textarea:focus {
        border-color: #bb86fc; box-shadow: 0 0 0 3px rgba(187, 134, 252, 0.2);
    }
    .dark-mode .form-control[readonly] { background-color: #383838; }

    .action-bar {
        margin-top: 30px; text-align: right; border-top: 1px solid #eee; padding-top: 20px;
    }
    .dark-mode .action-bar { border-top: 1px solid #3d3d3d; }

    .action-btn {
        padding: 10px 25px; border-radius: 8px; border: none; cursor: pointer;
        font-size: 14px; font-weight: 600; transition: all 0.3s;
        display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
    }
    .action-btn.save { background: #667eea; color: white; }
    .action-btn.save:hover { background: #5568d3; color: white; box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4); }
    .action-btn.back { background: #6c757d; color: white; margin-right: 10px; }
    .action-btn.back:hover { background: #5a6268; color: white; }

    .dark-mode .action-btn.save { background: #bb86fc; color: #000; }
    .dark-mode .action-btn.save:hover { background: #9a73c7; }

    .alert-danger {
        background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;
        padding: 15px; border-radius: 8px; margin-bottom: 20px;
    }
</style>

{{-- Dark Mode Toggle Button --}}
<button class="dark-mode-toggle" id="darkModeToggle" title="Toggle Dark Mode">
    <i class="fa fa-moon-o"></i>
</button>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">

            {{-- Header Section --}}
            <div class="task-header">
                <h2><i class="fa fa-edit"></i> Edit Task</h2>
                <p>Task ID: #{{ $task->task_id }}</p>
            </div>

            {{-- Error Validation Handling --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="details-card">
                        <h3>Update Task Details</h3>

                        <form action="{{ route('tasks.update', $task->task_id) }}" method="POST" autocomplete="off">
                            @csrf
                            @method('PUT')

                            {{-- Department --}}
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label control-label">Department</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="dept_id" id="dept_id" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->dept_id }}"
                                                {{ (old('dept_id', $task->dept_id) == $dept->dept_id) ? 'selected' : '' }}>
                                                {{ $dept->dept_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- 1. Task Title Field --}}
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label control-label">Task Title <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text"
                                           class="form-control"
                                           name="t_title"
                                           id="t_title"
                                           value="{{ old('t_title', $task->t_title) }}"
                                           placeholder="Enter task title"
                                           required>
                                </div>
                            </div>

                            {{-- 2. Start Time Field --}}
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label control-label">Start Time <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control datetimepicker"
                                               name="t_start_time"
                                               id="t_start_time"
                                               value="{{ old('t_start_time', $task->t_start_time) }}"
                                               placeholder="Select Start Date & Time"
                                               required>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. End Time Field --}}
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label control-label">End Time <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control datetimepicker"
                                               name="t_end_time"
                                               id="t_end_time"
                                               value="{{ old('t_end_time', $task->t_end_time) }}"
                                               placeholder="Select End Date & Time"
                                               required>
                                    </div>
                                </div>
                            </div>

                            {{-- 4. Status Field --}}
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label control-label">Status <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="">Select Status</option>
                                        <option value="0" {{ (old('status', $task->status) == 0) ? 'selected' : '' }}>Incomplete</option>
                                        <option value="1" {{ (old('status', $task->status) == 1) ? 'selected' : '' }}>In Progress</option>
                                        <option value="2" {{ (old('status', $task->status) == 2) ? 'selected' : '' }}>Complete</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Optional Description Field (Often goes with tasks) --}}
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label control-label">Description</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" name="t_description" rows="3">{{ old('t_description', $task->t_description ?? '') }}</textarea>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="action-bar">
                                <a href="{{ route('tasks.index') }}" class="action-btn back">
                                    <i class="fa fa-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" class="action-btn save">
                                    <i class="fa fa-save"></i> Update Task
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 1. Initialize Flatpickr for Time Fields
        flatpickr(".datetimepicker", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
            allowInput: true
        });

        // 2. Dark Mode Toggle Logic
        const toggleBtn = document.getElementById('darkModeToggle');
        const icon = toggleBtn.querySelector('i');
        const body = document.body;

        // Check local storage for preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            icon.classList.remove('fa-moon-o');
            icon.classList.add('fa-sun-o');
        }

        toggleBtn.addEventListener('click', () => {
            body.classList.toggle('dark-mode');

            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                icon.classList.remove('fa-moon-o');
                icon.classList.add('fa-sun-o');
            } else {
                localStorage.setItem('darkMode', 'disabled');
                icon.classList.remove('fa-sun-o');
                icon.classList.add('fa-moon-o');
            }
        });
    });
</script>

@endsection
