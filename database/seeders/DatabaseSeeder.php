<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['slug' => 'administrator'], [
            'name' => 'Administrator',
            'description' => 'Full system access with all permissions',
            'is_active' => true,
        ]);

        $managerRole = Role::firstOrCreate(['slug' => 'manager'], [
            'name' => 'Manager',
            'description' => 'Can manage users and view reports',
            'is_active' => true,
        ]);

        $editorRole = Role::firstOrCreate(['slug' => 'editor'], [
            'name' => 'Editor',
            'description' => 'Can create and edit content',
            'is_active' => true,
        ]);

        $viewerRole = Role::firstOrCreate(['slug' => 'viewer'], [
            'name' => 'Viewer',
            'description' => 'Read-only access to content',
            'is_active' => true,
        ]);

        // Create users
        $users = User::factory(50)->create();

        // Create test user with admin role
        $testUser = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);

        // Assign roles to users
        $testUser->roles()->attach($adminRole);

        // Assign roles to random users
        foreach ($users->random(10) as $user) {
            $user->roles()->attach(fake()->randomElement([$managerRole, $editorRole, $viewerRole]));
        }

        // Create products
        Product::factory(100)->create();
    }
}
