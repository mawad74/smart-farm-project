<?php

use App\Models\Farm;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Default User',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $farm = Farm::firstOrCreate([
            'name' => 'Default Farm',
            'location' => 'Default Location',
            'user_id' => $user->id,
        ]);

        \App\Models\Sensor::firstOrCreate([
            'type' => 'temperature',
            'name' => 'Temperature Sensor',
            'unit' => 'Celsius',
            'status' => 'active',
            'farm_id' => $farm->id,
        ]);

        \App\Models\Sensor::firstOrCreate([
            'type' => 'humidity',
            'name' => 'Humidity Sensor',
            'unit' => 'Percentage',
            'status' => 'active',
            'farm_id' => $farm->id,
        ]);

        \App\Models\Sensor::firstOrCreate([
            'type' => 'soil_moisture',
            'name' => 'Soil Moisture Sensor',
            'unit' => 'Percentage',
            'status' => 'active',
            'farm_id' => $farm->id,
        ]);

        \App\Models\Sensor::firstOrCreate([
            'type' => 'ldr',
            'name' => 'LDR Sensor',
            'unit' => 'Lux',
            'status' => 'active',
            'farm_id' => $farm->id,
        ]);

        \App\Models\Sensor::all()->each(function ($sensor) {
            $sensor->sensorData()->create([
                'value' => 0, // Initial value
            ]);
        });
    }
}
