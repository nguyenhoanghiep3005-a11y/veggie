<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    // Goi cac seeder du lieu mau dang duoc su dung.
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
            RolePermissionsTableSeeder::class,
            UsersTableSeeder::class,
            AdminTableSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
