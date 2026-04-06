<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash; // Import Hash

class PasswordController extends Controller
{
    public function changeTempPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6',
            're_password' => 'required|same:password',
        ]);

        // Assuming User model maps to the same table as Admin
        $user = User::find(Session::get('admin_id'));

        // CHANGE: Hash the password before saving
        $user->update([
            'password' => Hash::make($request->password),
            'temp_password' => null, // Use null for empty database fields usually
        ]);

        return redirect('/tasks');
    }

    public function changePassword(Request $request)
    {
        $id = Session::get('admin_id');
        $user = User::find($id);

        // 1. Verify Old Password (Check Bcrypt first, then fallback to MD5)
        $currentPassword = $request->admin_old_password;

        $isCorrect = false;

        // Check if DB has Bcrypt
        if (Hash::check($currentPassword, $user->password)) {
            $isCorrect = true;
        }
        // Check if DB has MD5 (Legacy)
        elseif ($user->password === md5($currentPassword)) {
            $isCorrect = true;
        }

        if (!$isCorrect) {
            return back()->withErrors(['msg' => 'Invalid old password']);
        }

        if ($request->admin_new_password !== $request->admin_cnew_password) {
            return back()->withErrors(['msg' => 'Passwords do not match']);
        }

        // 2. Save New Password as Bcrypt
        $user->update([
            'password' => Hash::make($request->admin_new_password),
        ]);

        return redirect('/manage-admin')->with('success', 'Password changed successfully');
    }
}
