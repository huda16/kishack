<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
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

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
