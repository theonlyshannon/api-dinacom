<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'account-management',

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
        ];

        $admin = Role::firstOrCreate(['name' => 'admin']);

        $doctor = Role::firstOrCreate(['name' => 'doctor']);

        $user = Role::firstOrCreate(['name' => 'user']);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin->syncPermissions([
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
        ]);

        $doctor->syncPermissions([

        ]);

        $user->syncPermissions([

        ]);
    }
}
