<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            1 => 'admin',
            2 => 'staff',
            3 => 'customer',
        ];

        foreach ($roles as $id => $name) {
            DB::table('roles')->updateOrInsert(
                ['id' => $id],
                ['name' => $name, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
