<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'nguyenvana@example.com'], [
            'name' => 'Nguyen Van A',
            'password' => bcrypt('123456'),
            'phone_number' => '0123456789',
            'status' => 'active',
            'address' => 'Da Nang, Vietnam',
            'role_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::updateOrCreate(['email' => 'tranthib@example.com'], [
            'name' => 'Tran Thi B',
            'password' => bcrypt('123456'),
            'phone_number' => '0987654321',
            'status' => 'active',
            'address' => 'Gia Lai, Vietnam',
            'role_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::updateOrCreate(['email' => 'nguyenhoanghiep@example.com'], [
            'name' => 'Nguyen Hoang Hiep',
            'password' => bcrypt('123456'),
            'phone_number' => '0987654321',
            'status' => 'active',
            'address' => 'Ho Chi Minh, Vietnam',
            'role_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
