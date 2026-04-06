<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Department;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // ==================== CONFIGURATION & HELPERS ====================
    private const ROLE_ADMIN = 1;
    private const ROLE_EMPLOYEE = 2;
    private const IT_DEPT_NAME = 'IT';

    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    private function isITAdmin(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        if (!$user->relationLoaded('department')) {
            $user->load('department');
        }

        if ($user->department && strtolower($user->department->dept_name) === strtolower(self::IT_DEPT_NAME)) {
            return true;
        }

        return false;
    }

    // ==================== MANAGE ADMINS ====================

    public function manageAdmin()
    {
        $admins = Admin::with('department')
            ->where('user_role', self::ROLE_ADMIN)
            ->orderBy('fullname', 'asc')
            ->get();

        $departments = Department::orderBy('dept_name')->get();

        return view('admin.manage-admin', [
            'admins'      => $admins,
            'departments' => $departments,
            'isITAdmin'   => $this->isITAdmin()
        ]);
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'em_fullname' => 'required|string|max:120',
            'em_username' => ['required', 'string', 'max:100', 'unique:tbl_admin,username', 'regex:/^[a-zA-Z0-9_-]{3,20}$/'],
            'em_contact'  => 'nullable|string|max:50',
            'dept_id'     => 'required|exists:departments,dept_id',
            'em_password' => 'required|string|min:6',
        ]);

        Admin::create([
            'fullname'      => $validated['em_fullname'],
            'username'      => $validated['em_username'],
            'contact'       => $validated['em_contact'] ?? '',
            'dept_id'       => $validated['dept_id'],
            'password'      => md5($validated['em_password']),
            'temp_password' => null,
            'user_role'     => self::ROLE_ADMIN,
        ]);

        return redirect()->route('admin.manage')
                         ->with('success', 'New Admin created successfully!');
    }

    public function editAdmin($id)
    {
        $admin = Admin::findOrFail($id);
        $currentUser = Auth::user();

        if ($currentUser->user_id != $admin->user_id && !$this->isITAdmin()) {
            return redirect()->route('admin.manage')
                             ->with('error', 'You do not have permission to edit this profile.');
        }

        $departments = Department::orderBy('dept_name')->get();

        return view('admin.edit-admin', compact('admin', 'departments'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $currentUser = Auth::user();

        if ($currentUser->user_id != $admin->user_id && !$this->isITAdmin()) {
            return redirect()->route('admin.manage')->with('error', 'Access Denied.');
        }

        $validated = $request->validate([
            'em_fullname' => 'required|string|max:120',
            'em_username' => [
                'required', 'string', 'max:100',
                Rule::unique('tbl_admin', 'username')->ignore($admin->user_id, 'user_id')
            ],
            'em_contact'  => 'nullable|string|max:50',
            'dept_id'     => 'nullable|exists:departments,dept_id',
        ]);

        $admin->update([
            'fullname' => $validated['em_fullname'],
            'username' => $validated['em_username'],
            'contact'  => $validated['em_contact'] ?? '',
            'dept_id'  => $validated['dept_id'],
        ]);

        return redirect()->route('admin.manage')
                         ->with('success', 'Profile updated successfully!');
    }

    // ==================== PASSWORD MANAGEMENT (SELF) ====================

    public function changePassword($id)
    {
        $admin = Admin::findOrFail($id);
        $currentUserId = Auth::id();

        if ($currentUserId != $admin->user_id && !$this->isITAdmin()) {
            return redirect()->route('admin.manage')->with('error', 'Access denied.');
        }

        return view('admin.change-password', compact('admin'));
    }

    public function updatePassword(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        if (Auth::id() != $admin->user_id && !$this->isITAdmin()) {
            return redirect()->route('admin.manage')->with('error', 'Access denied.');
        }

        $request->validate([
            'admin_new_password'  => 'required|min:6',
            'admin_cnew_password' => 'required|same:admin_new_password',
        ]);

        $admin->update([
            'password' => md5($request->admin_new_password)
        ]);

        return redirect()->route('admin.manage')->with('success', 'Password has been updated.');
    }

    // ==================== MANAGE USERS/EMPLOYEES ====================

    public function manageUsers(Request $request)
    {
        $user = Auth::user();
        $departments = Department::orderBy('dept_name')->get();

        $query = Admin::with('department')->where('user_role', self::ROLE_EMPLOYEE);

        if (!$this->isITAdmin()) {
            $query->where('dept_id', $user->dept_id);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('fullname', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('username', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('contact', 'LIKE', "%{$searchTerm}%");
            });
        }

        $employees = $query->orderBy('fullname')->paginate(20)->appends($request->query());

        return view('admin.manage-users', [
            'employees'     => $employees,
            'departments'   => $departments,
            'admin_dept_id' => $user->dept_id,
            'isITAdmin'     => $this->isITAdmin()
        ]);
    }

    public function editUser($id)
    {
        $employee = Admin::findOrFail($id);
        $currentUser = Auth::user();

        if (!$this->isITAdmin() && $currentUser->dept_id != $employee->dept_id) {
            return redirect()->route('admin.manage.users')
                             ->with('error', 'You do not have permission to edit this employee.');
        }

        $departments = Department::orderBy('dept_name')->get();
        return view('admin.edit-user', compact('employee', 'departments'));
    }

    public function storeUser(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'em_fullname' => 'required|string|max:120',
            'em_username' => ['required', 'string', 'max:100', 'unique:tbl_admin,username', 'regex:/^[a-zA-Z0-9_-]{3,20}$/'],
            'em_contact'  => 'nullable|string|max:50',
            'dept_id'     => 'required|exists:departments,dept_id',
            'em_password' => 'required|string|min:6',
        ]);

        if (!$this->isITAdmin() && $validated['dept_id'] != $user->dept_id) {
            return back()->withErrors(['dept_id' => 'You can only add employees to your own department.']);
        }

        Admin::create([
            'fullname'      => $validated['em_fullname'],
            'username'      => $validated['em_username'],
            'contact'       => $validated['em_contact'] ?? '',
            'dept_id'       => $validated['dept_id'],
            'password'      => md5($validated['em_password']),
            'temp_password' => null,
            'user_role'     => self::ROLE_EMPLOYEE,
        ]);

        return redirect()->route('admin.manage.users')
                         ->with('success', 'Employee added successfully!');
    }

    public function updateUser(Request $request, $id)
    {
        $employee = Admin::findOrFail($id);

        $validated = $request->validate([
            'em_fullname' => 'required|string|max:120',
            'em_username' => [
                'required', 'string', 'max:100',
                Rule::unique('tbl_admin', 'username')->ignore($employee->user_id, 'user_id')
            ],
            'em_contact'  => 'nullable|string|max:50',
            'dept_id'     => 'required|exists:departments,dept_id',
            'new_password'=> 'nullable|string|min:6'
        ]);

        $updateData = [
            'fullname' => $validated['em_fullname'],
            'username' => $validated['em_username'],
            'contact'  => $validated['em_contact'] ?? '',
            'dept_id'  => $validated['dept_id'],
        ];

        if ($request->filled('new_password')) {
            $updateData['password'] = md5($request->new_password);
        }

        $employee->update($updateData);

        return redirect()->route('admin.manage.users')
                         ->with('success', 'Employee updated successfully!');
    }

    public function deleteUser($id)
    {
        $employee = Admin::findOrFail($id);
        $user = Auth::user();

        if (!$this->isITAdmin() && $employee->dept_id != $user->dept_id) {
             return redirect()->route('admin.manage.users')->with('error', 'Access denied.');
        }

        // Delete associated tasks first
        Task::where('t_user_id', $employee->user_id)->delete();

        $employee->delete();

        return redirect()->route('admin.manage.users')
                         ->with('success', 'Employee deleted successfully!');
    }

    // ==================== SPECIAL PASSWORD RESET FUNCTIONS ====================

    /**
     * RESET ANY PASSWORD (Used by Admins to reset others)
     * Route: /admin/reset-any-password/{id}
     */
    public function resetAnyPassword(Request $request, $id)
    {
        $targetUser = Admin::findOrFail($id);
        $currentUser = Auth::user();

        $request->validate(['new_password' => 'required|string|min:6']);

        // --- PERMISSION CHECK ---

        // CASE 1: Target is an Admin
        if ($targetUser->user_role == self::ROLE_ADMIN) {
            // Only IT Super Admin can reset other Admins (unless it's themselves)
            if (!$this->isITAdmin() && $currentUser->user_id != $targetUser->user_id) {
                return back()->with('error', 'Only IT Super Admins can reset other Admin passwords.');
            }
        }
        // CASE 2: Target is an Employee
        else {
            // IT Admin OR Department Head (of same dept) can reset
            if (!$this->isITAdmin() && $currentUser->dept_id != $targetUser->dept_id) {
                return back()->with('error', 'You can only reset passwords for employees in your own department.');
            }
        }

        // --- UPDATE PASSWORD ---
        $targetUser->update([
            'password'      => md5($request->new_password),
            'temp_password' => null
        ]);

        return back()->with('success', 'Password successfully reset for ' . $targetUser->fullname);
    }

    /**
     * UPDATE USER PASSWORD (Used by Manage Employee page modal)
     * Route: /admin/users/{id}/password-update
     */
    public function updateUserPassword(Request $request, $id)
    {
        return $this->resetAnyPassword($request, $id);
    }
}
