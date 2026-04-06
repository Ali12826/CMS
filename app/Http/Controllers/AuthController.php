<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import Hash Facade
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ==================== LOGIN ====================

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('tasks.index');
        }
        return view('auth.login');
    }

public function login(Request $request)
    {
        // 1. Validate Input
        $request->validate([
            'username'       => 'required|string',
            'admin_password' => 'required|string',
        ]);

        $username = $request->username;
        $plainPassword = $request->admin_password;

        // 2. Find the user
        $admin = Admin::where('username', $username)->first();

        if ($admin) {
            $storedPassword = $admin->password;

            // === HYBRID CHECK: MD5 vs BCRYPT ===

            // CASE A: Legacy MD5 Password (Length is exactly 32 characters)
            if (strlen($storedPassword) === 32) {
                if ($storedPassword === md5($plainPassword)) {
                    // 1. Password matches MD5
                    // 2. Upgrade them to Bcrypt immediately
                    $admin->password = Hash::make($plainPassword);
                    $admin->save();

                    return $this->loginUser($request, $admin);
                }
            }
            // CASE B: Standard Bcrypt Password (Length is usually 60 characters)
            else {
                // Only call Hash::check on strings that look like Bcrypt
                if (Hash::check($plainPassword, $storedPassword)) {
                    return $this->loginUser($request, $admin);
                }
            }
        }

        // 3. Failed Login Response
        throw ValidationException::withMessages([
            'username' => ['❌ Invalid Username or Password'],
        ]);
    }

    // Helper to handle the session logic to avoid code duplication
    private function loginUser($request, $admin)
    {
        Auth::login($admin);

        Session::put([
            'admin_id'       => $admin->user_id,
            'name'           => $admin->fullname,
            'user_role'      => $admin->user_role,
            'user_dept_id'   => $admin->dept_id,
            'temp_password'  => $admin->temp_password,
            'login_time'     => now(),
        ]);

        if (!empty($admin->temp_password)) {
            return redirect()->route('change.password.employee');
        }

        $request->session()->regenerate();

        return redirect()->route('tasks.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ==================== REGISTRATION ====================

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('tasks.index');
        }
        $departments = Department::orderBy('dept_name')->get();
        return view('auth.register', compact('departments'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:120',
            'username' => ['required', 'string', 'max:100', 'unique:tbl_admin,username', 'regex:/^[a-zA-Z0-9_-]{3,20}$/'],
            'contact'  => 'required|string|max:20',
            'dept_id'  => 'required|exists:departments,dept_id',
            'password' => 'required|string|min:6|confirmed',
        ]);

        Admin::create([
            'fullname'  => $request->fullname,
            'username'  => $request->username,
            'contact'   => $request->contact,
            'dept_id'   => $request->dept_id,
            // CHANGE: Use Hash::make() instead of md5()
            'password'  => Hash::make($request->password),
            'user_role' => 2,
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    // ==================== FORCE PASSWORD CHANGE ====================

    public function showChangePasswordEmployee()
    {
        if (!Auth::check() || empty(Auth::user()->temp_password)) {
            return redirect()->route('tasks.index');
        }
        return view('auth.change-password-employee');
    }

    public function changePasswordEmployee(Request $request)
    {
        $request->validate([
            'password'    => 'required|string|min:6',
            're_password' => 'required|same:password',
        ]);

        /** @var \App\Models\Admin $user */
        $user = Auth::user();

        $user->update([
            // CHANGE: Use Hash::make() instead of md5()
            'password'      => Hash::make($request->password),
            'temp_password' => null
        ]);

        Session::forget('temp_password');

        return redirect()->route('tasks.index')->with('success', 'Password updated successfully!');
    }
}
