@extends('app')

@section('content')
<div class="container-fluid">

    {{-- Styles --}}
    <style>
        /* Reusing your custom styles */
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
            background: white; border-radius: 15px; padding: 25px;
            margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 5px solid #667eea;
        }

        /* Form & Table */
        .form-control { border-radius: 6px; height: 40px; }
        .btn-add { background: #28a745; color: white; border: none; padding: 10px 25px; border-radius: 6px; font-weight: 600; }
        .btn-add:hover { background: #218838; color: white; }

        .table-custom th { background: #f4f6f9; color: #333; font-weight: 700; border-bottom: 2px solid #ddd; }
        .table-custom td { vertical-align: middle !important; }

        /* Action Buttons */
        .btn-icon { width: 32px; height: 32px; padding: 0; line-height: 32px; text-align: center; border-radius: 4px; border: none; margin-right: 5px; color: white; display: inline-block; }
        .btn-edit { background: #17a2b8; }
        .btn-key { background: #ffc107; color: #333; }
        .btn-delete { background: #dc3545; }

        .badge-dept { background: #e2e6ea; color: #333; padding: 5px 10px; border-radius: 12px; font-size: 11px; }
    </style>

    {{-- 1. HEADER --}}
    <div class="row">
        <div class="col-md-12">
            <div class="task-header">
                <h2><i class="fa fa-user-tie"></i> Manage Employees</h2>
                <p>Add, edit, and manage employee credentials</p>
            </div>
        </div>
    </div>

    {{-- 2. TABS & CONTENT --}}
    <div class="row">
        <div class="col-md-12">

            <ul class="nav nav-tabs nav-tabs-custom">
                <li><a href="{{ route('admin.manage') }}"><i class="fa fa-users"></i> Manage Admin</a></li>
                <li class="active"><a href="{{ route('admin.manage.users') }}"><i class="fa fa-user-tie"></i> Manage Employee</a></li>
            </ul>

            {{-- SEARCH BAR SECTION --}}
            <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-6">
                    {{-- Updated to show Total from Paginator --}}
                    <h5 style="margin-top: 10px; font-weight: 600; color: #555;">
                        Total Employees: <span class="badge" style="background: #667eea;">{{ $employees->total() }}</span>
                    </h5>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('admin.manage.users') }}" method="GET">
                        {{-- Preserve Department ID if filtering by dept --}}
                        @if(request('dept_id'))
                            <input type="hidden" name="dept_id" value="{{ request('dept_id') }}">
                        @endif

                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search by Name, Username or Contact..."
                                   value="{{ request('search') }}"
                                   style="border-radius: 6px 0 0 6px;">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-primary" style="height: 40px; border-radius: 0 6px 6px 0; background: #001f3f;">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </span>
                            {{-- Clear Search Button (only shows if searching) --}}
                            @if(request('search'))
                                <span class="input-group-btn" style="padding-left: 5px;">
                                    <a href="{{ route('admin.manage.users') }}" class="btn btn-default" style="height: 40px; border-radius: 6px; border: 1px solid #ccc;">
                                        <i class="fa fa-times"></i> Clear
                                    </a>
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            {{-- END SEARCH BAR SECTION --}}

            {{-- ADD EMPLOYEE SECTION --}}
            <div class="task-card">
                <h4 style="margin-top:0; margin-bottom:20px; font-weight:700; color:#333;">
                    <i class="fa fa-plus-circle"></i> Add New Employee
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

                <form action="{{ route('admin.store.user') }}" method="POST" class="form-horizontal" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <label>Full Name</label>
                            <input type="text" name="em_fullname" class="form-control" placeholder="John Doe" value="{{ old('em_fullname') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label>Username</label>
                            <input type="text" name="em_username" class="form-control" placeholder="johndoe" value="{{ old('em_username') }}" required>
                        </div>
                        <div class="col-md-2">
                            <label>Email/Contact</label>
                            <input type="text" name="em_email" class="form-control" placeholder="john@example.com" value="{{ old('em_email') }}" required>
                        </div>

                        {{-- NEW PASSWORD FIELD --}}
                        <div class="col-md-2">
                            <label>Password</label>
                            <input type="text" name="em_password" class="form-control" placeholder="Secret123" minlength="6" required>
                        </div>

                        <div class="col-md-2">
                            <label>Department</label>
                            <select name="dept_id" class="form-control" required {{ $admin_dept_id ? 'readonly' : '' }}>
                                <option value="">Select Dept</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->dept_id }}"
                                        {{ (old('dept_id', $admin_dept_id) == $dept->dept_id) ? 'selected' : '' }}>
                                        {{ $dept->dept_name }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- If readonly, we still need to send the value --}}
                            @if($admin_dept_id)
                                <input type="hidden" name="dept_id" value="{{ $admin_dept_id }}">
                            @endif
                        </div>

                        <div class="col-md-2" style="padding-top: 25px;">
                             <button type="submit" class="btn btn-add btn-block">
                                <i class="fa fa-check"></i> Add
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- EMPLOYEE LIST TABLE --}}
            <div class="task-card" style="padding: 0; overflow: hidden;">
                <div class="table-responsive">
                    <table class="table table-custom table-hover" style="margin: 0;">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Contact</th>
                                <th>Department</th>
                                <th class="text-center" width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $key => $employee)
                                <tr>
                                    {{-- UPDATED: Continuous row numbering across pages --}}
                                    <td>{{ $employees->firstItem() + $key }}</td>

                                    <td><strong>{{ $employee->fullname }}</strong></td>
                                    <td>{{ $employee->username }}</td>
                                    <td>{{ $employee->contact }}</td>
                                    <td>
                                        <span class="badge-dept">{{ $employee->department->dept_name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.edit.user', $employee->user_id) }}" class="btn-icon btn-edit" title="Edit Profile">
                                            <i class="fa fa-pencil"></i>
                                        </a>

                                        {{-- Password Reset Modal Trigger --}}
                                        <button type="button" class="btn-icon btn-key" title="Change Password"
                                                onclick="openPasswordModal('{{ $employee->user_id }}', '{{ $employee->fullname }}')">
                                            <i class="fa fa-key"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <a href="{{ route('admin.delete.user', $employee->user_id) }}" class="btn-icon btn-delete" title="Delete User"
                                           onclick="return confirm('Are you sure you want to delete {{ $employee->fullname }}? This will delete all their tasks.');">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center" style="padding: 20px; color: #777;">
                                        No employees found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- ADDED: Pagination Links --}}
                    <div style="padding: 10px 20px; text-align: right;">
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- PASSWORD RESET MODAL --}}
<div id="passwordModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header" style="background: #001f3f; color: white; border-radius: 10px 10px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color:white;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-key"></i> Reset Password</h4>
            </div>

            <form id="passwordForm" method="POST" action="">
                @csrf
                <div class="modal-body" style="padding: 25px;">
                    <p>Reset password for: <strong id="modalUserName" style="color:#667eea;"></strong></p>

                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Enter new password (min 6 chars)" minlength="6" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Handle Modal Data
    function openPasswordModal(userId, userName) {
        var form = document.getElementById('passwordForm');

        // FIX: Changed to match the route defined in web.php
        form.action = "{{ url('admin/reset-any-password') }}/" + userId;

        document.getElementById('modalUserName').innerText = userName;
        $('#passwordModal').modal('show');
    }
</script>

@endsection
