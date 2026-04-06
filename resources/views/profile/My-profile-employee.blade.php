@extends('app')

@section('content')
<div class="container-fluid">

    {{-- Styles --}}
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; padding: 30px; border-radius: 10px;
            margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .profile-header h2 { margin-top: 10px; font-weight: 700; color: white; }

        .profile-card {
            background: white; border-radius: 15px; padding: 40px;
            margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 5px solid #764ba2;
        }

        .form-control { border-radius: 6px; height: 40px; border: 1px solid #ddd; }
        .form-control[readonly] { background-color: #f9f9f9; cursor: not-allowed; }
        .control-label { font-weight: 600; color: #333; padding-top: 10px; text-align: right; }

        .btn-action { padding: 10px 25px; border-radius: 6px; font-weight: 600; border: none; }
        .btn-save { background: #28a745; color: white; }
        .btn-cancel { background: #f8f9fa; color: #666; border: 1px solid #ddd; }

        @media (max-width: 768px) { .control-label { text-align: left; margin-bottom: 5px; } }
    </style>

    {{-- 1. HEADER --}}
    <div class="row">
        <div class="col-md-12">
            <div class="profile-header">
                <h2><i class="fa fa-user-circle"></i> My Profile</h2>
                <p>Manage your account settings and password</p>
            </div>
        </div>
    </div>

    {{-- 2. MAIN CONTENT --}}
    <div class="row">
        <div class="col-md-12">

            <div class="profile-card">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">

                        <h4 style="margin-bottom: 25px; font-weight: 700; color: #333; text-align: center;">
                            Edit Your Details
                        </h4>

                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST" class="form-horizontal" autocomplete="off">
                            @csrf

                            {{-- Full Name --}}
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Full Name</label>
                                <div class="col-sm-9">
                                    <input type="text" name="fullname" class="form-control" value="{{ old('fullname', $user->fullname) }}" required>
                                </div>
                            </div>

                            {{-- Username --}}
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Username</label>
                                <div class="col-sm-9">
                                    <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                                </div>
                            </div>

                            {{-- Contact --}}
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Email/Contact</label>
                                <div class="col-sm-9">
                                    <input type="text" name="contact" class="form-control" value="{{ old('contact', $user->contact) }}" required>
                                </div>
                            </div>

                            {{-- Department (READ ONLY) --}}
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Department</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control"
                                           value="{{ $user->department ? $user->department->dept_name : 'No Department' }}"
                                           readonly>
                                    <small class="text-muted">Contact Admin to change department.</small>
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="form-group">
                                <label class="col-sm-3 control-label">New Password</label>
                                <div class="col-sm-9">
                                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                                    <small class="text-muted">Only fill this if you want to change your password.</small>
                                </div>
                            </div>

                            <hr style="margin: 30px 0;">

                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-action btn-save">
                                        <i class="fa fa-save"></i> Save Changes
                                    </button>
                                    <a href="{{ route('tasks.index') }}" class="btn btn-action btn-cancel" style="margin-left: 10px;">
                                        Back to Dashboard
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
