<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that users can have roles.
     */
    public function test_user_can_have_roles(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $user->roles()->attach($role);

        $this->assertCount(1, $user->roles);
        $this->assertEquals($role->name, $user->roles->first()->name);
    }

    /**
     * Test that roles can have users.
     */
    public function test_role_can_have_users(): void
    {
        $role = Role::factory()->create();
        $users = User::factory()->count(3)->create();

        $role->users()->attach($users);

        $this->assertCount(3, $role->users);
    }

    /**
     * Test hasRole method.
     */
    public function test_user_has_role_method(): void
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['slug' => 'administrator']);
        $editorRole = Role::factory()->create(['slug' => 'editor']);

        $user->roles()->attach($adminRole);

        $this->assertTrue($user->hasRole('administrator'));
        $this->assertFalse($user->hasRole('editor'));
    }

    /**
     * Test hasAnyRole method.
     */
    public function test_user_has_any_role_method(): void
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['slug' => 'administrator']);
        $editorRole = Role::factory()->create(['slug' => 'editor']);

        $user->roles()->attach($editorRole);

        $this->assertTrue($user->hasAnyRole(['administrator', 'editor']));
        $this->assertTrue($user->hasAnyRole(['editor', 'viewer']));
        $this->assertFalse($user->hasAnyRole(['administrator', 'manager']));
    }

    /**
     * Test that test user has administrator role.
     */
    public function test_test_user_has_administrator_role(): void
    {
        // Create test user with admin role like in the seeder
        $adminRole = Role::factory()->create(['slug' => 'administrator']);
        $testUser = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $testUser->roles()->attach($adminRole);
        
        $this->assertTrue($testUser->hasRole('administrator'));
    }
}
