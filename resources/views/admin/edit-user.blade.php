@extends('app')

@section('content')
<div class="container-fluid">

    {{-- Styles --}}
    <style>
        .task-header {
            background: linear-gradient(135deg, #0f0096ff 0%, #00d9ffff 100%);
            color: white; padding: 30px; border-radius: 10px;
            margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .task-header h2 { margin-top: 10px; font-weight: 700; color: white; }

        .nav-tabs-custom {
            border-bottom: 2px solid #e0e0e0; margin-bottom: 20px;
            background: white; border-radius: 10px 10px 0 0; padding: 0 10px;
        }
        .nav-tabs-custom > li > a {
            color: #001f3f; font-weight: 600; padding: 15px 20px;
            border: none; border-radius: 0; transition: all 0.3s ease;
        }
        .nav-tabs-custom > li > a:hover { background-color: #f0f8ff; color: #d4af37; }
        .nav-tabs-custom > li.active > a {
            color: #001f3f; background: transparent; border-bottom: 3px solid #d4af37;
        }

        .task-card {
            background: white; border-radius: 15px; padding: 40px;
            margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 5px solid #667eea;
        }

        .form-control { border-radius: 6px; height: 40px; border: 1px solid #ddd; }
        .control-label { font-weight: 600; color: #333; padding-top: 10px; text-align: right; }

        .btn-action { padding: 10px 25px; border-radius: 6px; font-weight: 600; border: none; }
        .btn-save { background: #28a745; color: white; }
        .btn-cancel { background: #f8f9fa; color: #666; border: 1px solid #ddd; }

        @media (max-width: 768px) { .control-label { text-align: left; margin-bottom: 5px; } }
    </style>

    {{-- 1. HEADER --}}
    <div class="row">
        <div class="col-md-12">
            <div class="task-header">
                <h2><i class="fa fa-user-tie"></i> Edit Employee</h2>
                <p>Update employee profile information</p>
            </div>
        </div>
    </div>

    {{-- 2. MAIN CONTENT --}}
    <div class="row">
        <div class="col-md-12">

            {{-- Tabs --}}
            <ul class="nav nav-tabs nav-tabs-custom">
                <li><a href="{{ route('admin.manage') }}"><i class="fa fa-users"></i> Manage Admin</a></li>
                <li class="active"><a href="{{ route('admin.manage.users') }}"><i class="fa fa-user-tie"></i> Manage Employee</a></li>
            </ul>

            <div class="task-card">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">

                        <h4 style="margin-bottom: 25px; font-weight: 700; color: #333; text-align: center;">
                            Update Employee Details
                        </h4>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.update.user', ['id' => $employee->user_id]) }}" method="POST" class="form-horizontal" autocomplete="off">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Full Name</label>
                                <div class="col-sm-9">
                                    <input type="text" name="em_fullname" class="form-control" value="{{ old('em_fullname', $employee->fullname) }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Username</label>
                                <div class="col-sm-9">
                                    <input type="text" name="em_username" class="form-control" value="{{ old('em_username', $employee->username) }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Email/Contact</label>
                                <div class="col-sm-9">
                                    <input type="text" name="em_contact" class="form-control" value="{{ old('em_contact', $employee->contact) }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Department</label>
                                <div class="col-sm-9">
                                    <select name="dept_id" class="form-control" required>
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->dept_id }}"
                                                {{ (old('dept_id', $employee->dept_id) == $dept->dept_id) ? 'selected' : '' }}>
                                                {{ $dept->dept_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- ADDED: Password Field (Optional) --}}
                            <div class="form-group">
                                <label class="col-sm-3 control-label">New Password</label>
                                <div class="col-sm-9">
                                    <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
                                    <small class="text-muted">Only fill this if you want to change the user's password.</small>
                                </div>
                            </div>

                            <hr style="margin: 30px 0;">

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-action btn-save">
                                        <i class="fa fa-check"></i> Update Employee
                                    </button>
                                    <a href="{{ route('admin.manage.users') }}" class="btn btn-action btn-cancel" style="margin-left: 10px;">
                                        Cancel
                                    </a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
