<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Admin Midori',
                'email'     => 'admin@midori.com',
                'role'      => 'admin',
                'is_active' => true,
                'password'  => Hash::make('password'),
            ],
            [
                'name'      => 'Kasir Midori',
                'email'     => 'kasir@midori.com',
                'role'      => 'kasir',
                'is_active' => true,
                'password'  => Hash::make('password'),
            ],
            [
                'name'      => 'Dapur Midori',
                'email'     => 'dapur@midori.com',
                'role'      => 'dapur',
                'is_active' => true,
                'password'  => Hash::make('password'),
            ],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(['email' => $data['email']], $data);
        }
    }
}
