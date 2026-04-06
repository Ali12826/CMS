<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Departments
        DB::table('departments')->insert([
            ['dept_id' => 1, 'dept_name' => 'HR'],
            ['dept_id' => 2, 'dept_name' => 'IT'],
            ['dept_id' => 3, 'dept_name' => 'Finance'],
        ]);

        // 2) Users (tbl_admin)
        // user_role: 1 = Admin/Manager, 2 = Employee (based on your code)
        DB::table('tbl_admin')->insert([
            ['user_id' => 1, 'fullname' => 'Manager One',  'user_role' => 1, 'dept_id' => 2],
            ['user_id' => 2, 'fullname' => 'Employee A',   'user_role' => 2, 'dept_id' => 2],
            ['user_id' => 3, 'fullname' => 'Employee B',   'user_role' => 2, 'dept_id' => 1],
        ]);

        // 3) Tasks (task_info)
        // IMPORTANT: adjust column names if your real schema is different.
        $now = Carbon::now();
        DB::table('task_info')->insert([
            // Tasks assigned to Employee A (user_id = 2), assigned by Manager (1)
            [
                'task_id'      => 1001,
                't_user_id'    => 2,                 // assignee
                'assigned_by'  => 1,                 // who assigned
                't_title'      => 'Update server docs',
                't_description'=> 'Prepare quick SOP for server restart steps.',
                't_start_time' => $now->copy()->subDays(2),
                't_end_time'   => $now->copy()->subDays(1),
                'status'       => 2,                 // completed
            ],
            [
                'task_id'      => 1002,
                't_user_id'    => 2,
                'assigned_by'  => 1,
                't_title'      => 'Fix login bug',
                't_description'=> 'Investigate session timeout issue in auth flow.',
                't_start_time' => $now->copy()->subDay(),
                't_end_time'   => $now->copy()->addDay(),
                'status'       => 1,                 // in progress
            ],

            // Tasks assigned to Employee B (user_id = 3), assigned by Manager (1)
            [
                'task_id'      => 1003,
                't_user_id'    => 3,
                'assigned_by'  => 1,
                't_title'      => 'Prepare payroll sheet',
                't_description'=> 'Collect attendance and calculate updated payroll.',
                't_start_time' => $now->copy()->subDays(3),
                't_end_time'   => $now->copy()->subDays(1),
                'status'       => 0,                 // incomplete
            ],
        ]);
    }
}
