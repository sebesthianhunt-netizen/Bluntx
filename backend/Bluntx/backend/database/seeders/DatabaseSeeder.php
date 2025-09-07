<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Venue;
use App\Models\SnookerTable;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Minimal seed: one venue with several tables
        $venue = Venue::firstOrCreate([
            'name' => 'BLVKDOT Arena',
        ], [
            'address' => 'Lekki Phase 1, Lagos',
            'latitude' => 6.4400,
            'longitude' => 3.4700,
            'phone' => '+2348100000000',
            'is_active' => true,
        ]);

        for ($i = 1; $i <= 6; $i++) {
            SnookerTable::firstOrCreate([
                'venue_id' => $venue->id,
                'table_number' => $i,
            ], [
                'table_type' => $i <= 2 ? 'VIP' : 'Standard',
                'hourly_rate' => 2000 * 100,
            ]);
        }
    }
}
