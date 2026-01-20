<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = [
            ['name' => 'Administrator', 'slug' => 'administrator', 'description' => 'Full system access with all permissions'],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Can manage users and view reports'],
            ['name' => 'Editor', 'slug' => 'editor', 'description' => 'Can create and edit content'],
            ['name' => 'Viewer', 'slug' => 'viewer', 'description' => 'Read-only access to content'],
        ];

        $role = fake()->randomElement($roles);
        
        return [
            'name' => $role['name'],
            'slug' => $role['slug'],
            'description' => $role['description'],
            'is_active' => true,
        ];
    }
}
