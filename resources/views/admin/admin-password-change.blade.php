@extends('app')

@section('content')
<div class="container-fluid">

    {{-- Custom Styles --}}
    <style>
        /* Header Style */
        .task-header {
            background: linear-gradient(135deg, #0f0096ff 0%, #00d9ffff 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .task-header h2 { margin-top: 10px; font-weight: 700; color: white; }
        .task-header p { color: rgba(255,255,255,0.9); }

        /* Navigation Tabs */
        .nav-tabs-custom {
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 20px;
            background: white;
            border-radius: 10px 10px 0 0;
            padding: 0 10px;
        }
        .nav-tabs-custom > li > a {
            color: #001f3f;
            font-weight: 600;
            padding: 15px 20px;
            border: none;
            border-radius: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        .nav-tabs-custom > li > a:hover {
            background-color: #f0f8ff;
            color: #d4af37;
        }
        /* Active State */
        .nav-tabs-custom > li.active > a,
        .nav-tabs-custom > li.active > a:hover,
        .nav-tabs-custom > li.active > a:focus {
            color: #001f3f;
            background: transparent;
            border-bottom: 3px solid #d4af37;
        }

        /* Card Style */
        .task-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 5px solid #667eea;
        }

        /* Form Styling */
        .form-control {
            border-radius: 8px;
            height: 45px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            box-shadow: none;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .control-label {
            font-weight: 600;
            color: #333;
            padding-top: 10px;
            text-align: right;
        }

        /* Buttons */
        .btn-action {
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            transition: 0.3s;
            border: none;
        }
        .btn-save {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            color: white;
        }
        .btn-save:hover { box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3); color: white; }

        .btn-cancel {
            background: #f8f9fa;
            color: #666;
            border: 1px solid #ddd;
        }
        .btn-cancel:hover { background: #e2e6ea; color: #333; }

        @media (max-width: 768px) {
            .control-label { text-align: left; margin-bottom: 5px; }
        }
    </style>

    {{-- 1. HEADER --}}
    <div class="row">
        <div class="col-md-12">
            <div class="task-header">
                <h2><i class="fa fa-key"></i> Change Password</h2>
                <p>Securely update your admin account password</p>
            </div>
        </div>
    </div>

    {{-- 2. TABS & FORM --}}
    <div class="row">
        <div class="col-md-12">

            {{-- Navigation Tabs --}}
            <ul class="nav nav-tabs nav-tabs-custom">
                {{-- Active class highlights that we are in the Admin section --}}
                <li class="active">
                    <a href="{{ route('admin.manage') }}">
                        <i class="fa fa-users"></i> Manage Admin
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.manage.users') }}">
                        <i class="fa fa-user-tie"></i> Manage Employee
                    </a>
                </li>
            </ul>

            <div class="task-card">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">

                        <h3 class="text-center" style="margin-bottom: 30px; color: #001f3f; font-weight: 700;">
                            Update Admin22 Credentials
                        </h3>

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

                        {{-- Password Form --}}
                        <form action="{{ route('admin.update_password', $admin->user_id) }}" method="POST" class="form-horizontal" autocomplete="off" onsubmit="return validatePassword()">
                            @csrf

                            {{-- Old Password --}}
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Current Password <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" name="admin_old_password" class="form-control" placeholder="Enter current password" required>
                                </div>
                            </div>

                            {{-- New Password --}}
                            <div class="form-group">
                                <label class="col-sm-4 control-label">New Password <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" name="admin_new_password" id="new_pass" class="form-control" placeholder="Enter new password (min 6 chars)" minlength="6" required>
                                </div>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Confirm New Password <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="password" name="admin_cnew_password" id="confirm_pass" class="form-control" placeholder="Re-enter new password" required>
                                </div>
                            </div>

                            <hr style="margin: 30px 0;">

                            {{-- Actions --}}
                            <div class="form-group">
                                <div class="col-sm-offset-4 col-sm-8">
                                    <button type="submit" class="btn btn-action btn-save">
                                        <i class="fa fa-check-circle"></i> Update Password
                                    </button>
                                    <a href="{{ route('admin.manage') }}" class="btn btn-action btn-cancel" style="margin-left: 10px;">
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

<script>
    // Client-side validation fallback
    function validatePassword() {
        var newPass = document.getElementById('new_pass').value;
        var confirmPass = document.getElementById('confirm_pass').value;

        if (newPass !== confirmPass) {
            alert("New passwords do not match!");
            return false;
        }
        return true;
    }
</script>

@endsection
