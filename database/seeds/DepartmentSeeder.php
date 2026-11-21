<?php

use App\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            'Office & Admin',
            'Warehouse & Logistics',
            'Field Sales & Delivery',
            'Equipment & Maintenance',
        ];

        foreach ($departments as $name) {
            Department::firstOrCreate(
                ['department_name' => $name],
                ['status' => 'Active']
            );
        }
    }
}
