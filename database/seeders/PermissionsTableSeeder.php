<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $permissions = [
        'manage_user',
        'manage_products',
        'manage_orders',
        'manage_categories',
        'manage_contacts',
      ];
       foreach($permissions as $permissions)
       {
        Permission::create(['name' =>$permissions]);
       }
    }
}
