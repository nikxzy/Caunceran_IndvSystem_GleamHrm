<?php

use App\Branch;
use App\Client;
use App\Department;
use App\Designation;
use App\Employee;
use App\Hub;
use App\InventoryStock;
use App\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PeamsSeeder extends Seeder
{
    public function run()
    {
        $hubs = $this->seedHubs();
        $branches = $this->seedBranches();
        $departments = $this->seedDepartments();
        $this->seedDesignations();
        $roles = $this->seedRoles();
        $this->syncRolePermissions($roles);
        $this->seedCatalog($hubs);
        $this->seedClients();
        $this->seedWorkforce($hubs, $branches, $departments, $roles);
    }

    protected function seedHubs()
    {
        return [
            'bongabong' => Hub::firstOrCreate(
                ['code' => 'BONG'],
                [
                    'name' => 'Bongabong Central Hub',
                    'address' => 'Bongabong, Oriental Mindoro',
                    'contact_person' => 'Hub Administrator',
                    'contact_no' => '0917-000-0001',
                    'is_active' => true,
                ]
            ),
            'roxas' => Hub::firstOrCreate(
                ['code' => 'ROXAS'],
                [
                    'name' => 'Roxas Branch Office',
                    'address' => 'Roxas, Oriental Mindoro',
                    'contact_person' => 'Branch Supervisor',
                    'contact_no' => '0917-000-0002',
                    'is_active' => true,
                ]
            ),
            'regional' => Hub::firstOrCreate(
                ['code' => 'REG'],
                [
                    'name' => 'Regional Delivery & On-site Staff',
                    'address' => 'Mindoro-wide field operations',
                    'contact_person' => 'Field Operations Lead',
                    'contact_no' => '0917-000-0003',
                    'is_active' => true,
                ]
            ),
        ];
    }

    protected function seedBranches()
    {
        $data = [
            'Bongabong Central Hub' => ['Bongabong, Oriental Mindoro', '08:00:00', '17:00:00', '["Sunday"]'],
            'Roxas Branch Office' => ['Roxas, Oriental Mindoro', '08:00:00', '17:00:00', '["Sunday"]'],
            'Regional Delivery & On-site Staff' => ['Mindoro-wide field operations', '07:00:00', '16:00:00', '["Sunday"]'],
        ];

        $branches = [];
        foreach ($data as $name => $values) {
            $branches[$name] = Branch::updateOrCreate(
                ['name' => $name, 'address' => $values[0]],
                [
                    'status' => 1,
                    'timing_start' => $values[1],
                    'timing_off' => $values[2],
                    'weekend' => $values[3],
                ]
            );
        }

        return $branches;
    }

    protected function seedDepartments()
    {
        $names = ['Office & Admin', 'Warehouse & Logistics', 'Field Sales & Delivery', 'Equipment & Maintenance'];
        $departments = [];
        foreach ($names as $name) {
            $departments[$name] = Department::firstOrCreate(['department_name' => $name], ['status' => 'Active']);
        }

        return $departments;
    }

    protected function seedDesignations()
    {
        $names = [
            'Warehouse Manager', 'Inventory Clerk', 'Logistics Coordinator', 'Order Picker',
            'Equipment Technician', 'Field Sales Representative', 'Delivery Driver',
            'HR & Admin Officer', 'Branch Supervisor', 'Branch Cashier',
            'Account Executive', 'Maintenance Lead', 'Operations Assistant',
        ];

        foreach ($names as $name) {
            Designation::firstOrCreate(['designation_name' => $name], ['status' => 1]);
        }
    }

    protected function seedRoles()
    {
        $templates = [
            'Office & Admin',
            'Warehouse & Logistics',
            'Field Sales & Delivery',
            'Equipment Technician',
        ];

        $roles = [];
        foreach ($templates as $name) {
            $roles[$name] = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        return $roles;
    }

    protected function syncRolePermissions(array $roles)
    {
        if (Permission::count() === 0) {
            return;
        }

        $roles['Office & Admin']->syncPermissions(Permission::all());

        $templates = [
            'Warehouse & Logistics' => [
                'DashboardController', 'InventoryController', 'OrderController',
                'TransferController', 'EquipmentLoanController', 'AssetDisposalController',
            ],
            'Field Sales & Delivery' => [
                'DashboardController', 'InventoryController', 'OrderController', 'EquipmentLoanController',
            ],
            'Equipment Technician' => [
                'DashboardController', 'InventoryController', 'MaintenanceController',
                'EquipmentLoanController', 'AssetDisposalController',
            ],
        ];

        foreach ($templates as $roleName => $controllers) {
            $permissions = Permission::where(function ($query) use ($controllers) {
                foreach ($controllers as $controller) {
                    $query->orWhere('name', 'like', $controller.':%');
                }
            })->get();

            $roles[$roleName]->syncPermissions($permissions);
        }
    }

    protected function seedCatalog(array $hubs)
    {
        $catalog = [
            // Beverage packaging
            ['BEV-PET12', 'PET Cup 12oz', 'packaging', 'Beverage Packaging', 'pc', 4.50, 300],
            ['BEV-PAP8', 'Paper Cup 8oz', 'packaging', 'Beverage Packaging', 'pc', 3.25, 300],
            ['BEV-BOBALID', 'Boba Flat Lid', 'packaging', 'Beverage Packaging', 'pc', 1.10, 500],
            ['BEV-DOMELID', 'Dome Lid 12oz', 'packaging', 'Beverage Packaging', 'pc', 1.35, 400],
            ['BEV-STRAW', 'Wide Boba Straw', 'packaging', 'Beverage Packaging', 'pc', 0.55, 800],
            // Bakery packaging
            ['BAK-CUPBOX', 'Cupcake Box (6pcs)', 'packaging', 'Bakery Packaging', 'pc', 8.50, 150],
            ['BAK-CAKEBRD', 'Cake Board 10in', 'packaging', 'Bakery Packaging', 'pc', 6.00, 120],
            ['BAK-PASTRY', 'Pastry Box', 'packaging', 'Bakery Packaging', 'pc', 5.25, 200],
            ['BAK-CAKEBOX', 'Cake Box 8x8', 'packaging', 'Bakery Packaging', 'pc', 12.00, 100],
            // Store machinery assets
            ['EQP-CUPSEAL', 'Commercial Cup Sealing Machine', 'equipment', 'Store Machinery Assets', 'unit', 18500.00, 3],
            ['EQP-ESPRESSO', 'Commercial Espresso Machine', 'equipment', 'Store Machinery Assets', 'unit', 42000.00, 2],
            ['EQP-BLENDER', 'High-Speed Blender', 'equipment', 'Store Machinery Assets', 'unit', 6500.00, 4],
            ['EQP-GRINDER', 'Coffee Grinder', 'equipment', 'Store Machinery Assets', 'unit', 7800.00, 3],
            ['EQP-SHAKER', 'Bubble Tea Shaker Machine', 'equipment', 'Store Machinery Assets', 'unit', 9500.00, 2],
        ];

        $items = [];
        foreach ($catalog as $row) {
            list($sku, $name, $type, $category, $unit, $cost, $reorder) = $row;
            $isEquipment = $type === 'equipment';

            $items[$sku] = Item::updateOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'type' => $type,
                    'category' => $category,
                    'unit' => $unit,
                    'description' => $name.' supplied by Mindoro Pack & Pour Warehousing Solutions.',
                    'unit_cost' => $cost,
                    'reorder_level' => $reorder,
                    'is_loanable' => in_array($sku, ['EQP-CUPSEAL', 'EQP-BLENDER', 'EQP-SHAKER'], true),
                    'requires_preventive_maintenance' => $isEquipment,
                    'service_interval_days' => $isEquipment ? 90 : null,
                    'last_serviced_at' => $isEquipment ? now()->subDays(30)->toDateString() : null,
                    'next_service_at' => $isEquipment ? now()->addDays(60)->toDateString() : null,
                    'is_active' => true,
                ]
            );
        }

        $stockLevels = [
            'bongabong' => [
                'BEV-PET12' => 1200, 'BEV-PAP8' => 950, 'BEV-BOBALID' => 1800, 'BEV-DOMELID' => 640,
                'BEV-STRAW' => 2400, 'BAK-CUPBOX' => 320, 'BAK-CAKEBRD' => 180, 'BAK-PASTRY' => 95,
                'BAK-CAKEBOX' => 60, 'EQP-CUPSEAL' => 2, 'EQP-ESPRESSO' => 1, 'EQP-BLENDER' => 3,
                'EQP-GRINDER' => 2, 'EQP-SHAKER' => 1,
            ],
            'roxas' => [
                'BEV-PET12' => 240, 'BEV-PAP8' => 180, 'BEV-BOBALID' => 420, 'BEV-DOMELID' => 160,
                'BEV-STRAW' => 600, 'BAK-CUPBOX' => 70, 'BAK-CAKEBRD' => 45, 'BAK-PASTRY' => 38,
                'BAK-CAKEBOX' => 22, 'EQP-CUPSEAL' => 1, 'EQP-BLENDER' => 1, 'EQP-GRINDER' => 1,
            ],
        ];

        foreach ($stockLevels as $hubKey => $levels) {
            foreach ($levels as $sku => $quantity) {
                InventoryStock::updateOrCreate(
                    ['hub_id' => $hubs[$hubKey]->id, 'item_id' => $items[$sku]->id],
                    ['quantity' => $quantity, 'reserved_quantity' => 0]
                );
            }
        }
    }

    protected function seedClients()
    {
        $clients = [
            ['Mango Frappe Corner', 'Alyssa Cruz', 'mango.frappe@example.com', '0918-111-2201', 'Bongabong, Oriental Mindoro'],
            ['Sweet Crumb Bakeshop', 'Marco Reyes', 'sweetcrumb@example.com', '0918-111-2202', 'Roxas, Oriental Mindoro'],
            ['Bubble House Milk Tea', 'Kim Lopez', 'bubblehouse@example.com', '0918-111-2203', 'Bongabong, Oriental Mindoro'],
            ['Cafe Mindoro', 'Daniel Santos', 'cafemindoro@example.com', '0918-111-2204', 'Roxas, Oriental Mindoro'],
            ['Pasya Cupcakes', 'Erika Villanueva', 'pasyacakes@example.com', '0918-111-2205', 'Bongabong, Oriental Mindoro'],
        ];

        foreach ($clients as $client) {
            Client::firstOrCreate(
                ['name' => $client[0]],
                [
                    'contact_person' => $client[1],
                    'email' => $client[2],
                    'phone' => $client[3],
                    'address' => $client[4],
                    'is_active' => true,
                ]
            );
        }
    }

    protected function seedWorkforce(array $hubs, array $branches, array $departments, array $roles)
    {
        $bongabongBranch = $branches['Bongabong Central Hub'];
        $roxasBranch = $branches['Roxas Branch Office'];
        $regionalBranch = $branches['Regional Delivery & On-site Staff'];

        $people = [];

        // Office & Admin — 7
        $admin = [
            ['Maria', 'Delos Santos', 'HR & Admin Officer'],
            ['Jose', 'Bautista', 'Branch Supervisor'],
            ['Angelica', 'Ramos', 'Account Executive'],
            ['Paolo', 'Mendoza', 'Branch Cashier'],
            ['Cristina', 'Garcia', 'Operations Assistant'],
            ['Ramon', 'Aquino', 'Branch Supervisor'],
            ['Bea', 'Navarro', 'HR & Admin Officer'],
        ];
        foreach ($admin as $i => $person) {
            $people[] = [
                'name' => $person,
                'department' => 'Office & Admin',
                'role' => 'Office & Admin',
                'hub' => $i < 5 ? 'bongabong' : 'roxas',
                'branch' => $i < 5 ? $bongabongBranch : $roxasBranch,
                'status' => 'permanent',
            ];
        }

        // Warehouse & Logistics — 9 (6 logistics + 3 equipment technicians)
        $warehouse = [
            ['Lito', 'Fernandez', 'Warehouse Manager'],
            ['Grace', 'Torres', 'Inventory Clerk'],
            ['Mark', 'Salazar', 'Logistics Coordinator'],
            ['Nina', 'Domingo', 'Order Picker'],
            ['Erwin', 'Castillo', 'Inventory Clerk'],
            ['Joel', 'Rivera', 'Logistics Coordinator'],
            ['Arnel', 'Pascual', 'Equipment Technician'],
            ['Michelle', 'Ocampo', 'Equipment Technician'],
            ['Rico', 'Gonzales', 'Maintenance Lead'],
        ];
        foreach ($warehouse as $i => $person) {
            $people[] = [
                'name' => $person,
                'department' => 'Warehouse & Logistics',
                'role' => $i >= 6 ? 'Equipment Technician' : 'Warehouse & Logistics',
                'hub' => $i < 6 ? 'bongabong' : 'roxas',
                'branch' => $i < 6 ? $bongabongBranch : $roxasBranch,
                'status' => 'permanent',
            ];
        }

        // Field Sales & Delivery — 6
        $field = [
            ['Adrian', 'Lim', 'Field Sales Representative'],
            ['Karla', 'Estrada', 'Field Sales Representative'],
            ['Ben', 'Vargas', 'Delivery Driver'],
            ['Rhea', 'Morales', 'Delivery Driver'],
            ['Dennis', 'Aguilar', 'Delivery Driver'],
            ['Lorna', 'Padilla', 'Field Sales Representative'],
        ];
        foreach ($field as $i => $person) {
            $people[] = [
                'name' => $person,
                'department' => 'Field Sales & Delivery',
                'role' => 'Field Sales & Delivery',
                'hub' => $i < 3 ? 'bongabong' : 'regional',
                'branch' => $i < 3 ? $bongabongBranch : $regionalBranch,
                'status' => 'contractual',
            ];
        }

        foreach ($people as $index => $person) {
            list($firstname, $lastname, $designation) = $person['name'];
            $email = strtolower($firstname.'.'.$lastname).'@mindoropackpour.com';

            $employee = Employee::firstOrCreate(
                ['official_email' => $email],
                [
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'contact_no' => '0917'.str_pad((string) (1000000 + $index), 7, '0', STR_PAD_LEFT),
                    'personal_email' => strtolower($firstname.$lastname).'@example.com',
                    'password' => Hash::make('Password@123'),
                    'designation' => $designation,
                    'employment_status' => $person['status'],
                    'status' => 1,
                    'is_active' => true,
                    'gender' => $index % 2 === 0 ? 'Male' : 'Female',
                    'joining_date' => now()->subMonths(6 + $index)->toDateString(),
                    'gross_salary' => 18000 + ($index * 350),
                    'city' => 'Oriental Mindoro',
                    'current_address' => $person['branch']->address,
                    'permanent_address' => $person['branch']->address,
                    'branch_id' => $person['branch']->id,
                    'hub_id' => $hubs[$person['hub']]->id,
                    'department_id' => $departments[$person['department']]->id,
                    'type' => in_array($person['role'], ['Field Sales & Delivery'], true) ? 'remote' : 'office',
                ]
            );

            if (! $employee->hasRole($person['role'])) {
                $employee->assignRole($roles[$person['role']]);
            }
        }
    }
}
