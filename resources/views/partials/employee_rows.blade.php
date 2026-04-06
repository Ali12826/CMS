{{-- resources/views/admin/partials/employee_rows.blade.php --}}

@forelse($employees as $key => $employee)
    <tr>
        <td>{{ $key + 1 }}</td>
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

            {{-- Password Reset --}}
            <button type="button" class="btn-icon btn-key" title="Change Password"
                    onclick="openPasswordModal('{{ $employee->user_id }}', '{{ $employee->fullname }}')">
                <i class="fa fa-key"></i>
            </button>

            {{-- Delete --}}
            <a href="{{ route('admin.delete.user', $employee->user_id) }}" class="btn-icon btn-delete" title="Delete User"
               onclick="return confirm('Are you sure you want to delete {{ $employee->fullname }}?');">
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
