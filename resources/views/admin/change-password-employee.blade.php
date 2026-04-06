@extends('app')

@section('content')
<div class="container">
    <div class="row" style="margin-top: 60px;">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default" style="border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <div class="panel-heading" style="background: #001f3f; color: white; border-radius: 10px 10px 0 0; padding: 15px;">
                    <h3 class="panel-title" style="font-weight: 700; margin: 0;">
                        <i class="fa fa-lock"></i> Change Password
                    </h3>
                </div>
                <div class="panel-body" style="padding: 30px;">
                    <p class="text-muted" style="margin-bottom: 20px;">
                        For security reasons, please update your password before proceeding.
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul style="margin-bottom: 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('change.password.employee.post') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>New Password888888</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter new password (min 6 chars)" minlength="6" required>
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="re_password" class="form-control" placeholder="Re-enter password" minlength="6" required>
                        </div>

                        <div class="form-group" style="margin-top: 25px;">
                            <button type="submit" class="btn btn-primary btn-block" style="background-color: #001f3f; border: none; padding: 10px;">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
