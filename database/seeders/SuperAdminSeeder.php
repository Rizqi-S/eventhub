<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('SUPER_ADMIN_NAME');
        $email = env('SUPER_ADMIN_EMAIL');
        $password = env('SUPER_ADMIN_PASSWORD');

        if (! $name || ! $email || ! $password) {
            throw new RuntimeException('Isi SUPER_ADMIN_NAME, SUPER_ADMIN_EMAIL, dan SUPER_ADMIN_PASSWORD di .env sebelum menjalankan seeder.');
        }

        User::updateOrCreate(
            ['email' => strtolower($email)],
            ['name' => $name, 'password' => Hash::make($password), 'is_super_admin' => true]
        );

        $admins = [
            [
                'name' => 'Admin 1',
                'email' => 'admin1@eventhub.test',
                'password' => 'Admin1Pass!2026',
            ],
            [
                'name' => 'Admin 2',
                'email' => 'admin2@eventhub.test',
                'password' => 'Admin2Pass!2026',
            ],
            [
                'name' => 'Admin 3',
                'email' => 'admin3@eventhub.test',
                'password' => 'Admin3Pass!2026',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => strtolower($admin['email'])],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make($admin['password']),
                    'is_super_admin' => true,
                ]
            );
        }
    }
}
