<?php

use App\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $admin = Employee::firstOrCreate(
            ['official_email' => 'admin@mindoropackpour.com'],
            [
                'firstname' => 'System',
                'lastname' => 'Administrator',
                'contact_no' => '09170000000',
                'emergency_contact' => '09170000000',
                'emergency_contact_relationship' => 'Office',
                'password' => Hash::make('admin'),
                'identity_no' => 'PEAMS-ADMIN-0001',
                'date_of_birth' => '1990-01-01',
                'personal_email' => 'admin@mindoropackpour.com',
                'designation' => 'HR & Admin Officer',
                'city' => 'Bongabong',
                'current_address' => 'Bongabong, Oriental Mindoro',
                'permanent_address' => 'Bongabong, Oriental Mindoro',
                'joining_date' => '2026-01-01',
                'branch_id' => 1,
                'status' => 1,
                'is_active' => true,
                'employment_status' => 'permanent',
            ]
        );

        $role = Role::where('name', 'admin')->first();
        if ($role && ! $admin->hasRole('admin')) {
            $admin->assignRole($role);
        }
    }
}
