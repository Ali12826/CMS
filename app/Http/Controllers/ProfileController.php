<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the "My Profile" form
     */
    public function showProfile()
    {
        // Get the currently logged-in user
        $user = Auth::user();

        // We load the department just to display the name (read-only)
        $user->load('department');

        return view('profile.My-profile-employee', compact('user'));
    }

    /**
     * Update "My Profile" data
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user(); // Get current user

        $validated = $request->validate([
            'fullname' => 'required|string|max:120',
            // Ensure username is unique but ignore the current user's own username
            'username' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tbl_admin', 'username')->ignore($user->user_id, 'user_id')
            ],
            'contact'  => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6' // Optional password change
        ]);

        // Prepare update data
        $data = [
            'fullname' => $validated['fullname'],
            'username' => $validated['username'],
            'contact'  => $validated['contact'] ?? '',
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = md5($request->password);
        }

        // Perform the update on the logged-in user instance
        // We do NOT update dept_id here (employees shouldn't move themselves)
        Admin::where('user_id', $user->user_id)->update($data);

        return redirect()->route('profile.show')
                         ->with('success', 'Your profile has been updated successfully!');
    }
}
