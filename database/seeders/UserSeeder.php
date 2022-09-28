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
        User::create([
            'name' => 'Juan Ruiz',
            'phone' => '3002121457',
            'email' => 'test@gmail.com',
            'profile' => 'ADMIN',
            'status' => 'ACTIVE',
            'password' => bcrypt('123456*')
        ]);

        User::create([
            'name' => 'Test User',
            'phone' => '300212145',
            'email' => 'testuser@gmail.com',
            'profile' => 'EMPLOYEE',
            'status' => 'ACTIVE',
            'password' => bcrypt('123456')
        ]);
    }
}
