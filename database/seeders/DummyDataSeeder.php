<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // 1. Define the Departments you want to populate
        // (Assuming IDs 1 to 5 based on your previous SQL file)
        $departmentIds = [1, 2, 3, 4, 5];

        $totalEmployees = 0;
        $totalTasks = 0;

        echo "Starting Data Generation...\n";

        foreach ($departmentIds as $deptId) {
            echo "Processing Department ID: $deptId...\n";

            // 2. Create 20 Employees per Department
            for ($i = 0; $i < 20; $i++) {

                $employeeId = DB::table('tbl_admin')->insertGetId([
                    'fullname'      => $faker->name,
                    'username'      => $faker->unique()->userName . '_' . rand(100, 999), // Ensure uniqueness
                    'contact'       => $faker->phoneNumber,
                    'dept_id'       => $deptId,
                    'password'      => md5('123456'), // Default password for everyone
                    'temp_password' => null,
                    'user_role'     => 2, // 2 = Employee
                ]);

                $totalEmployees++;

                // 3. Create 50 Tasks for THIS Employee
                for ($j = 0; $j < 50; $j++) {
                    DB::table('task_info')->insert([
                        't_title'       => $faker->jobTitle . ' Task',
                        'dept_id'       => $deptId,
                        't_description' => $faker->paragraph(2),
                        // Random dates within last month and next month
                        't_start_time'  => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i'),
                        't_end_time'    => $faker->dateTimeBetween('now', '+1 month')->format('Y-m-d H:i'),
                        't_user_id'     => $employeeId,
                        'assigned_by'   => 1, // Assuming Super Admin ID 1 assigns the tasks
                        'status'        => $faker->numberBetween(0, 2), // 0, 1, or 2
                    ]);
                    $totalTasks++;
                }
            }
        }

        echo "\nSuccess! Created $totalEmployees employees and $totalTasks tasks.\n";
    }
}
