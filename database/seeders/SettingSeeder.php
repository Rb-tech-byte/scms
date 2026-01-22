<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settingData=[
            'system_name'=>'Institute of Finance Management',
        ];

        Setting::create($settingData);
    }
}
