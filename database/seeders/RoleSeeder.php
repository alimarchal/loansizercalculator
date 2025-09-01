<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $borrowerRole = Role::firstOrCreate(['name' => 'borrower']);

        // Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_super_admin' => 'Yes',
                'is_active' => 'Yes',
                'email_verified_at' => now(),
            ]
        );

        // Assign superadmin role to the super admin user
        if (!$superAdmin->hasRole('superadmin')) {
            $superAdmin->assignRole('superadmin');
        }

        // Create Borrower user
        $borrower = User::firstOrCreate(
            ['email' => 'borrower@example.com'],
            [
                'name' => 'Borrower User',
                'password' => Hash::make('password'),
                'is_super_admin' => 'No',
                'is_active' => 'Yes',
                'email_verified_at' => now(),
            ]
        );

        // Assign borrower role to the borrower user
        if (!$borrower->hasRole('borrower')) {
            $borrower->assignRole('borrower');
        }
    }
}
