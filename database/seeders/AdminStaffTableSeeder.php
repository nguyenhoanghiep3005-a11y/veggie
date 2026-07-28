<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminStaffTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin',
            'password' => bcrypt('123456'),
            'phone_number' => '019999999',
            'status' => 'active',
            'address' => 'Da Nang, Vietnam',
            'role_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'staff@example.com'], [
            'name' => 'Staff',
            'password' => bcrypt('123456'),
            'phone_number' => '018889999',
            'status' => 'active',
            'address' => 'Da Nang, Vietnam',
            'role_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
