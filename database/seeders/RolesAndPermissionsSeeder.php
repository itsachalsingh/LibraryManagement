<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $superadmin = Role::create(['name' => 'superadmin']);
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);

        // Create book permissions
        $viewBooks = Permission::create(['name' => 'view books']);
        $manageBooks = Permission::create(['name' => 'manage books']); // For superadmin and admin
        $editOwnBooks = Permission::create(['name' => 'edit own books']); // For user to edit their own books
        $deleteOwnBooks = Permission::create(['name' => 'delete own books']); // For user to delete their own books
        $createOwnBooks = Permission::create(['name' => 'create own books']); // For user to create their own books

        // Create user permissions
        $viewUsers = Permission::create(['name' => 'view users']);
        $manageUsers = Permission::create(['name' => 'manage users']); // Superadmin can manage users

        // Assign permissions to roles
        // Superadmin can manage all books and users
        $superadmin->givePermissionTo([
            $viewBooks, $manageBooks, $editOwnBooks, $deleteOwnBooks, $createOwnBooks,
            $viewUsers, $manageUsers
        ]);

        // Admin can only view books and users, cannot manage them
        $admin->givePermissionTo([$viewBooks, $viewUsers]);

        // User can only create, edit, and delete their own books
        $user->givePermissionTo([$viewBooks, $createOwnBooks, $editOwnBooks, $deleteOwnBooks]);

        // Create Superadmin user
        $superadminUser = User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password123'),
        ]);
        $superadminUser->assignRole('superadmin');

        // Create Admin user
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
        $adminUser->assignRole('admin');

    }
}
