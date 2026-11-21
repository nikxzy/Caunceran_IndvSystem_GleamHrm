<?php

use App\Designation;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    public function run()
    {
        $designations = [
            'Warehouse Manager',
            'Inventory Clerk',
            'Logistics Coordinator',
            'Order Picker',
            'Equipment Technician',
            'Field Sales Representative',
            'Delivery Driver',
            'HR & Admin Officer',
            'Branch Supervisor',
            'Branch Cashier',
            'Account Executive',
            'Maintenance Lead',
            'Operations Assistant',
        ];

        foreach ($designations as $designation) {
            Designation::firstOrCreate(
                ['designation_name' => $designation],
                ['status' => 1]
            );
        }
    }
}
