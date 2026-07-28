<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            1 => 'manage_user',
            2 => 'manage_products',
            3 => 'manage_order',
            4 => 'manage_categories',
        ];

        foreach ($permissions as $id => $name) {
            DB::table('permissions')->updateOrInsert(
                ['id' => $id],
                ['name' => $name, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
