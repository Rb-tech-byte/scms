<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branchData = [
            [
                'name' => 'Head Quarters Branch',
                'location' => 'Dar es Salaam',
            ],
            [
                'name' => 'Dodoma Branch',
                'location' => 'Dodoma',
            ],
            [
                'name' => 'Arusha Branch',
                'location' => 'Arusha',
            ],
        ];

        foreach ($branchData as $branch) {
            Branch::create($branch);
        }
    }
}
