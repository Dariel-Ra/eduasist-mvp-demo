<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear Super Administrador
        User::firstOrCreate(
            ['email' => 'sysadmin@example.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'name' => 'Super Admin', // Opcional
                'phone' => '+1234567890',
                'role' => 'sysadmin',
                'status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Crear Administrador
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'name' => 'Admin User',
                'phone' => '+1234567891',
                'role' => 'admin',
                'status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Crear Profesor
        User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            [
                'first_name' => 'Maria',
                'last_name' => 'García',
                'name' => 'Maria García',
                'phone' => '+1234567892',
                'role' => 'teacher',
                'status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Crear Tutor/Guardian
        User::firstOrCreate(
            ['email' => 'guardian@example.com'],
            [
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'name' => 'Juan Pérez',
                'phone' => '+1234567893',
                'role' => 'guardian',
                'status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Crear usuarios adicionales con factory (opcional)
        // User::factory(10)->create();
    }
}