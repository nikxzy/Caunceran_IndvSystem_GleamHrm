<?php

use Illuminate\Database\Seeder;
use App\EmployeeLeaveType;
use App\Employee;

class EmployeeLeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = Employee::all();

        if ($employees->count() > 0) {
            foreach ($employees as $employee) {
                EmployeeLeaveType::firstOrCreate([
                    'employee_id' => $employee->id,
                    'leave_type_id' => 1,
                ], [
                    'count' => 12,
                ]);

                EmployeeLeaveType::firstOrCreate([
                    'employee_id' => $employee->id,
                    'leave_type_id' => 2,
                ], [
                    'count' => 12,
                ]);
            }
        }
    }
}