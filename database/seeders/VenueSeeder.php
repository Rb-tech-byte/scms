<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venuData = [
            [
                'name' => 'Head Quarters',
                'branch_id' => 1,
            ]
        ];

        foreach ($venuData as $venu) {
            Venue::create($venu);
        }
    }
}
