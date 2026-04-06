@extends('app')

@section('content')

<div class="container-fluid">

    {{-- STYLES --}}
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
            background: white; border-radius: 15px; padding: 25px;
            margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 5px solid #667eea;
        }
        .form-control { border-radius: 6px; height: 40px; }
        .btn-add { background: #28a745; color: white; border: none; padding: 10px 25px; border-radius: 6px; font-weight: 600; }
        .btn-add:hover { background: #218838; color: white; }
        .btn-icon { width: 30px; height: 30px; padding: 0; line-height: 30px; text-align: center; border-radius: 4px; border: none; margin-right: 5px; color: white; display: inline-block; }
        .btn-edit { background: #17a2b8; }
        .btn-key { background: #ffc107; color: #333; }
        .btn-key:hover { background: #e0a800; }
        .table-custom th { background: #f4f6f9; color: #333; font-weight: 700; border-bottom: 2px solid #ddd; }
        .table-custom td { vertical-align: middle !important; }
        .badge-current { background: #667eea; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; margin-left: 8px; }
    </style>

    {{--
        ========================================
        SUCCESS / ERROR MESSAGES (ADDED HERE)
        ========================================
    --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" style="margin-bottom: 20px; border-radius: 5px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4><i class="icon fa fa-check"></i> Success!</h4>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" style="margin-bottom: 20px; border-radius: 5px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Error!</h4>
            {{ session('error') }}
        </div>
    @endif

    {{-- HEADER SECTION --}}
    <div class="row">
        <div class="col-md-12">
            <div class="task-header">
                <h2><i class="fa fa-users"></i> Manage Admin Accounts</h2>
                <p>Manage superuser credentials and profiles</p>
            </div>
        </div>
    </div>

    {{-- NAVIGATION TABS --}}
    <div class="row">
        <div class="col-md-12">

            <ul class="nav nav-tabs nav-tabs-custom">
                <li class="active"><a href="{{ route('admin.manage') }}"><i class="fa fa-users"></i> Manage Admin</a></li>
                <li><a href="{{ route('admin.manage.users') }}"><i class="fa fa-user-tie"></i> Manage Employee</a></li>
            </ul>

            {{-- ADD ADMIN FORM --}}
            <div class="task-card">
                <h4 style="margin-top:0; margin-bottom:20px; font-weight:700; color:#333;">
                    <i class="fa fa-plus-circle"></i> Add New Admin
                </h4>

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.store.admin') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-md-2">
                            <label>Full Name</label>
                            <input type="text" name="em_fullname" class="form-control" placeholder="Admin Name" value="{{ old('em_fullname') }}" required>
                        </div>

                        <div class="col-md-2">
                            <label>Email / Contact #</label>
                            <input type="text" name="em_contact" class="form-control" placeholder="Email or Phone Number" value="{{ old('em_contact') }}">
                        </div>

                        <div class="col-md-2">
                            <label>Username</label>
                            <input type="text" name="em_username" class="form-control" placeholder="admin_user" value="{{ old('em_username') }}" required>
                        </div>

                        <div class="col-md-2">
                            <label>Password</label>
                            <input type="text" name="em_password" class="form-control" placeholder="Min_length=6" minlength="6" required>
                        </div>

                        <div class="col-md-2">
                            <label>Department</label>
                            <select name="dept_id" class="form-control" required>
                                <option value="">Select Dept</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->dept_id }}">{{ $d->dept_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2" style="padding-top: 25px;">
                            <button type="submit" class="btn btn-add btn-block">
                                <i class="fa fa-check"></i> Create
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ADMIN LIST TABLE --}}
            <div class="task-card" style="padding: 0; overflow: hidden;">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Username</th>
                                <th>Department</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $index => $admin)
                                @php
                                    $isCurrentUser = ($admin->user_id == Auth::id());
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span style="font-weight: 600; color: #333;">{{ $admin->fullname }}</span>
                                        @if($isCurrentUser)
                                            <span class="badge-current"><i class="fa fa-star"></i> YOU</span>
                                        @endif
                                    </td>
                                    <td>{{ $admin->contact }}</td>
                                    <td>{{ $admin->username }}</td>
                                    <td>
                                        @if($admin->department)
                                            {{ $admin->department->dept_name }}
                                        @else
                                            <span class="text-danger">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Edit Profile (Self or IT Admin) --}}
                                        @if($isCurrentUser || $isITAdmin)
                                            <a href="{{ route('admin.edit', $admin->user_id) }}" class="btn-icon btn-edit" title="Edit Profile">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        @endif

                                        {{-- Reset Password (IT Admin only, but not for self via this button) --}}
                                        @if($isITAdmin && !$isCurrentUser)
                                            <button type="button" class="btn-icon btn-key" title="Reset Password"
                                                onclick="openPasswordModal('{{ $admin->user_id }}', '{{ $admin->fullname }}')">
                                                <i class="fa fa-key"></i>
                                            </button>
                                        @endif

                                        @if(!$isCurrentUser && !$isITAdmin)
                                            <span class="text-muted" style="font-style: italic; font-size: 12px;">
                                                <i class="fa fa-lock"></i> Protected
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                <h4 class="modal-title"><i class="fa fa-key"></i> Reset User Password</h4>
            </div>

            <form id="passwordForm" method="POST" action="">
                @csrf
                <div class="modal-body" style="padding: 25px;">
                    <p>Resetting password for: <strong id="modalUserName" style="color:#667eea;"></strong></p>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="text" name="new_password" class="form-control" placeholder="Enter new password" minlength="6" required>
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

{{-- JAVASCRIPT --}}
<script>
    function openPasswordModal(userId, userName) {
        var form = document.getElementById('passwordForm');

        // Dynamically set the URL to /admin/reset-any-password/{id}
        // This matches the Route::post('/admin/reset-any-password/{id}', ...) we defined.
        form.action = "{{ url('admin/reset-any-password') }}/" + userId;

        document.getElementById('modalUserName').innerText = userName;
        $('#passwordModal').modal('show');
    }
</script>

@endsection
