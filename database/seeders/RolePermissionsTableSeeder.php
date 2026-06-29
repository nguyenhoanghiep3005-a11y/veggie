<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy role admin và staff từ database
        $adminRole = Role::where('name', 'admin')->first();
        $staffRole = Role::where('name', 'staff')->first();

        // Lấy toàn bộ quyền
        $permissions = Permission::all();

        if (!$adminRole || !$staffRole) {
            return;
        }

        // Gán tất cả quyền cho admin (nếu tồn tại)
       
            $adminRole->permissions()->sync($permissions);
       

        // Gán một số quyền cho nhân viên (nếu tồn tại)
       
            $staffPermissions = $permissions->whereIn('name', [
                'manage_products',
                'manage_contacts',
            ]);

            $staffRole->permissions()->sync($staffPermissions);
        
    }
}
