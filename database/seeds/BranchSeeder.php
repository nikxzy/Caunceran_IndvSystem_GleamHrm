<?php

use App\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run()
    {
        $branches = [
            [
                'name' => 'Bongabong Central Hub',
                'address' => 'Bongabong, Oriental Mindoro',
                'timing_start' => '08:00:00',
                'timing_off' => '17:00:00',
                'weekend' => '["Sunday"]',
            ],
            [
                'name' => 'Roxas Branch Office',
                'address' => 'Roxas, Oriental Mindoro',
                'timing_start' => '08:00:00',
                'timing_off' => '17:00:00',
                'weekend' => '["Sunday"]',
            ],
            [
                'name' => 'Regional Delivery & On-site Staff',
                'address' => 'Mindoro-wide field operations',
                'timing_start' => '07:00:00',
                'timing_off' => '16:00:00',
                'weekend' => '["Sunday"]',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(
                ['name' => $branch['name'], 'address' => $branch['address']],
                array_merge($branch, ['status' => 1])
            );
        }
    }
}
