<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the products index page renders correctly.
     */
    public function test_products_index_renders(): void
    {
        // Create test products
        Product::factory()->count(5)->create();

        $response = $this->actingAs(User::factory()->create())
            ->get('/dashboard/products');

        $response->assertStatus(200);
    }

    /**
     * Test that products can be filtered by search.
     */
    public function test_products_can_be_filtered_by_search(): void
    {
        // Create specific products for testing
        Product::factory()->create([
            'name' => 'Laptop Pro',
            'description' => 'High-performance laptop for professionals',
            'category' => 'Electronics',
            'sku' => 'SKU-LAPTOP-001'
        ]);
        Product::factory()->create([
            'name' => 'Wireless Mouse',
            'description' => 'Ergonomic wireless mouse',
            'category' => 'Electronics',
            'sku' => 'SKU-MOUSE-002'
        ]);
        Product::factory()->create([
            'name' => 'Office Chair',
            'description' => 'Comfortable office chair',
            'category' => 'Furniture',
            'sku' => 'SKU-CHAIR-003'
        ]);

        // Test search by name
        $response = $this->actingAs(User::factory()->create())
            ->get('/dashboard/products?search=Laptop');

        $response->assertStatus(200);
        $response->assertSee('Laptop Pro');
        $response->assertDontSee('Wireless Mouse');
        $response->assertDontSee('Office Chair');

        // Test search by category
        $response = $this->actingAs(User::factory()->create())
            ->get('/dashboard/products?search=Electronics');

        $response->assertStatus(200);
        $response->assertSee('Laptop Pro');
        $response->assertSee('Wireless Mouse');
        $response->assertDontSee('Office Chair');

        // Test search by SKU
        $response = $this->actingAs(User::factory()->create())
            ->get('/dashboard/products?search=SKU-MOUSE');

        $response->assertStatus(200);
        $response->assertSee('Wireless Mouse');
        $response->assertDontSee('Laptop Pro');
        $response->assertDontSee('Office Chair');
    }

    /**
     * Test that products can be filtered by description.
     */
    public function test_products_can_be_filtered_by_description(): void
    {
        // Create specific products for testing
        Product::factory()->create([
            'name' => 'Gaming Keyboard',
            'description' => 'Mechanical keyboard with RGB lighting',
            'category' => 'Electronics'
        ]);
        Product::factory()->create([
            'name' => 'Standard Keyboard',
            'description' => 'Basic office keyboard',
            'category' => 'Electronics'
        ]);

        $response = $this->actingAs(User::factory()->create())
            ->get('/dashboard/products?search=mechanical');

        $response->assertStatus(200);
        $response->assertSee('Gaming Keyboard');
        $response->assertDontSee('Standard Keyboard');
    }

    /**
     * Test that unauthenticated users cannot access products page.
     */
    public function test_unauthenticated_users_cannot_access_products(): void
    {
        $response = $this->get('/dashboard/products');

        $response->assertRedirect('/login');
    }
}
