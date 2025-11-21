<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminStaffTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('123456'),
            'phone_number' => '019999999',
            'status' => 'active',
            'avatar' => '',
            'address' => 'Da Nang, Vietnam',
            'role_id' => 1,
            'created_at'=>now(),
            'updated_at'=>now()
        ]);

         User::create([
            'name' => 'Staff',
            'email' => 'staff@example.com',
            'password' => bcrypt('123456'),
            'phone_number' => '018889999',
            'status' => 'active',
            'avatar' => '',
            'address' => 'Da Nang, Vietnam',
            'role_id' => 2,
            'created_at'=>now(),
            'updated_at'=>now()
        ]);
    }
}
