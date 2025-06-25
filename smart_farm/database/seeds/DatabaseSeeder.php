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
        $user = \App\User::create([
            'name' => 'Default User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        Farm::firstOrCreate([
            'name' => 'Default Farm',
            'location' => 'Default Location',
            'user_id' => $user->id,
        ]);
    }
}
