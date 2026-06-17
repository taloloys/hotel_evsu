<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'full_name' => 'SoftwareAdmin',
                'password_hash' => 'password',
                'role' => 'ADMIN',
            ]
        );
    }
}
