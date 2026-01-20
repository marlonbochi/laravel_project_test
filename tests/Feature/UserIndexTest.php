<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the users index page renders correctly.
     */
    public function test_users_index_renders(): void
    {
        // Create test users
        User::factory()->count(5)->create();

        $response = $this->actingAs(User::factory()->create())
            ->get('/dashboard/users');

        $response->assertStatus(200);
    }

    /**
     * Test that users can be filtered by search.
     */
    public function test_users_can_be_filtered_by_search(): void
    {
        // Create specific users for testing
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);
        User::factory()->create(['name' => 'Bob Johnson', 'email' => 'bob@example.com']);

        $response = $this->actingAs(User::factory()->create())
            ->get('/dashboard/users?search=John');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('Bob Johnson');
        $response->assertDontSee('Jane Smith');
    }

    /**
     * Test that users can be filtered by email.
     */
    public function test_users_can_be_filtered_by_email(): void
    {
        // Create specific users for testing
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $response = $this->actingAs(User::factory()->create())
            ->get('/dashboard/users?search=jane');

        $response->assertStatus(200);
        $response->assertSee('Jane Smith');
        $response->assertDontSee('John Doe');
    }

    /**
     * Test that unauthenticated users cannot access the users page.
     */
    public function test_unauthenticated_cannot_access_users(): void
    {
        $response = $this->get('/dashboard/users');

        $response->assertRedirect('/login');
    }
}
