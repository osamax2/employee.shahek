<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin employee
        Employee::create([
            'name' => 'Administrator',
            'email' => env('ADMIN_EMAIL', 'admin@company.com'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
            'is_active' => true,
        ]);

        // Create test employees
        for ($i = 1; $i <= 5; $i++) {
            Employee::create([
                'name' => "Test Employee $i",
                'email' => "employee$i@company.com",
                'password' => Hash::make('password'),
                'device_id' => "test-device-$i",
                'is_active' => true,
            ]);
        }
    }
}
