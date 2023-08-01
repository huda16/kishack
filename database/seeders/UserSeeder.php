<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdminUser = User::create([
            'username' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('password'),
            'role_id' => 1
        ]);
        $superAdminUser->assignRole('Super Admin');
    }
}
