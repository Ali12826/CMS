<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Admin;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Constructor: Ensures that only logged-in users can access these methods.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the main Dashboard/Task List.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->user_role == 1; // 1 = Admin, 2 = Employee

        // ----------------------------------------------------------------
        // 1. FILTERING LOGIC (Main Task List)
        // ----------------------------------------------------------------

        // Default to 'to_me' (Tasks assigned to the logged-in user)
        $assignFilter = $request->input('assign_filter', 'to_me');

        // If Admin visits without a filter, show 'all' by default
        if ($isAdmin && !$request->has('assign_filter')) {
            $assignFilter = 'all';
        }

        // Start building the query with Eager Loading
        $query = Task::with(['department', 'assignedTo', 'assignedBy'])
                     ->orderBy('task_id', 'desc');

        // Apply "View Mode" Filter
        if ($assignFilter == 'to_me') {
            $query->where('t_user_id', $user->user_id);
        } elseif ($assignFilter == 'by_me') {
            $query->where('assigned_by', $user->user_id);
        }

        // Apply Search Filter (Title)
        if ($request->filled('q')) {
            $query->where('t_title', 'LIKE', '%' . $request->q . '%');
        }

        // Apply Department Filter
        // Logic: Get input value. If empty (first load), fallback to Auth user's dept.
        $deptFilter = $request->input('dept_id', $user->dept_id);

        if ($deptFilter != 'all') {
            $query->where('dept_id', $deptFilter);
        }

        // Apply Employee Filter
        if ($request->filled('employee_id') && $request->employee_id != 'all') {
            $query->where('t_user_id', $request->employee_id);
        }

        // Apply Status Filter
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // ----------------------------------------------------------------
        // 2. PAGINATION
        // ----------------------------------------------------------------
        $tasks = $query->paginate(20)->onEachSide(1)->withQueryString();

        // ----------------------------------------------------------------
        // 3. STATISTICS (TOP PANELS)
        // ----------------------------------------------------------------
        $statsQuery = DB::table('task_info');

        // If not admin, restrict stats to their own tasks
        if (!$isAdmin) {
             $statsQuery->where('t_user_id', $user->user_id);
        }

        $totalTasks = (clone $statsQuery)->count();
        $completed  = (clone $statsQuery)->where('status', 2)->count();
        $inProgress = (clone $statsQuery)->where('status', 1)->count();
        $incomplete = (clone $statsQuery)->where('status', 0)->count();

        // ----------------------------------------------------------------
        // 4. SIDEBAR DATA (Department Status)
        // ----------------------------------------------------------------
        $deptEmployees = DB::table('tbl_admin')
            ->join('departments', 'tbl_admin.dept_id', '=', 'departments.dept_id')
            // ✅ CHANGED: Allow both Admins (1) and Employees (2)
            ->whereIn('tbl_admin.user_role', [1, 2])
            ->where('tbl_admin.dept_id', $user->dept_id)
            // ✅ CHANGED: Added 'user_role' to select so Blade can show Badges
            ->select('tbl_admin.user_id', 'tbl_admin.fullname', 'tbl_admin.user_role', 'departments.dept_name')
            // ✅ CHANGED: Admins appear first, then sorted by name
            ->orderBy('tbl_admin.user_role', 'asc')
            ->orderBy('fullname')
            ->get();

        foreach($deptEmployees as $emp) {
            $activeTask = DB::table('task_info')
                ->where('t_user_id', $emp->user_id)
                ->whereIn('status', [0, 1])
                ->first();

            $emp->is_busy = $activeTask ? true : false;
            $emp->current_task = $activeTask ? $activeTask->t_title : '';
        }

        // ----------------------------------------------------------------
        // 5. DROPDOWN DATA (For Filters)
        // ----------------------------------------------------------------
        $departments = Department::orderBy('dept_name')->get();

        $employees = Admin::whereIn('user_role', [1, 2])
                          ->orderBy('fullname')
                          ->get();

        return view('tasks.index', compact(
            'tasks',
            'departments',
            'employees',
            'assignFilter',
            'totalTasks',
            'completed',
            'inProgress',
            'incomplete',
            'deptEmployees',
            'isAdmin'
        ));
    }

    /**
     * Show the form to create a new task.
     */
   public function create()
    {
        $departments = Department::orderBy('dept_name')->get();

        $employees = Admin::whereIn('user_role', [1, 2])
                          ->orderBy('fullname')
                          ->get();

        // Fetch all task types (we will filter them by dept in JavaScript)
        // Ensure you select 'dept_id' so we can link them
        $taskTypes = DB::table('task_types')
                        ->orderBy('task_name', 'asc')
                        ->get();

        return view('tasks.create', compact('departments', 'employees', 'taskTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            't_title'      => 'required|string|max:255',
            'dept_id'      => 'required',
            'location'     => 'nullable|string|max:255',
            't_start_time' => 'required|date',
            't_end_time'   => 'required|date|after_or_equal:t_start_time',
            't_user_id'    => 'required'
        ]);

        Task::create([
            't_title'       => $request->t_title,
            'dept_id'       => $request->dept_id,
            'location'      => $request->location,
            't_description' => $request->t_description,
            't_start_time'  => Carbon::parse($request->t_start_time),
            't_end_time'    => Carbon::parse($request->t_end_time),
            't_user_id'     => $request->t_user_id,
            'assigned_by'   => Auth::id(),
            'status'        => 0
        ]);
        return redirect()->route('tasks.index')->with('success', 'Task assigned successfully!');
    }

    /**
     * Display a specific task.
     */
    public function show($id)
    {
        $task = Task::with(['assignedTo', 'assignedBy', 'department'])->findOrFail($id);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the edit form.
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $departments = Department::orderBy('dept_name')->get();

        $employees = Admin::whereIn('user_role', [1, 2])
                          ->orderBy('fullname')
                          ->get();

        // [UPDATED] Use 'task_name' here as well
        $taskTypes = DB::table('task_types')->orderBy('task_name', 'asc')->get();

        return view('tasks.edit', compact('task', 'departments', 'employees', 'taskTypes'));
    }

    /**
     * Update a task.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            't_title'       => 'required|string|max:255',
        ]);

        $task = Task::findOrFail($id);
        $task->update($request->all());

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    /**
     * Delete a task.
     */
    public function destroy($id)
    {
        Task::destroy($id);
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }
}
