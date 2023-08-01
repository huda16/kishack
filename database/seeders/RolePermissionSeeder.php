<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdminPermissions = [
            'user.list',
            'user.create',
            'user.edit',
            'user.delete',
            'role_permission.list',
            'role_permission.create',
            'role_permission.edit',
            'role_permission.delete',
            'master_category.list',
            'master_category.create',
            'master_category.edit',
            'master_category.delete',
            'master_article.list',
            'master_article.create',
            'master_article.edit',
            'master_article.delete',
        ];

        $superAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $i = 1;
        foreach ($superAdminPermissions as $superAdminPermission) {
            $superAdmin->givePermissionTo($superAdminPermission);
        }
    }
}
