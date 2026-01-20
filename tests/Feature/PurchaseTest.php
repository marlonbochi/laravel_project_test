<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_index_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $purchase = Purchase::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/dashboard/purchases');

        $response->assertStatus(200);
    }

    public function test_purchase_can_be_created(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $purchaseData = [
            'user_id' => $user->id,
            'total_amount' => 100.00,
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'transaction_id' => 'test_transaction_' . uniqid(),
            'notes' => 'Test purchase note',
        ];

        $response = $this->actingAs($user)->post('/dashboard/purchases', $purchaseData);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchases', $purchaseData);
    }

    public function test_purchase_can_be_shown(): void
    {
        $user = User::factory()->create();
        $purchase = Purchase::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/dashboard/purchases/{$purchase->id}");

        $response->assertStatus(200);
    }

    public function test_purchase_can_be_updated(): void
    {
        $user = User::factory()->create();
        $purchase = Purchase::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'user_id' => $user->id,
            'total_amount' => 150.00,
            'status' => 'refunded',
            'payment_method' => 'paypal',
            'transaction_id' => $purchase->transaction_id,
            'notes' => 'Updated purchase note',
        ];

        $response = $this->actingAs($user)->put("/dashboard/purchases/{$purchase->id}", $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('purchases', $updatedData);
    }

    public function test_purchase_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $purchase = Purchase::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/dashboard/purchases/{$purchase->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);
    }

    public function test_purchase_can_have_products_attached(): void
    {
        $user = User::factory()->create();
        $purchase = Purchase::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();

        $purchase->products()->attach($product->id, [
            'quantity' => 2,
            'price' => $product->price,
            'subtotal' => $product->price * 2,
        ]);

        $this->assertDatabaseHas('purchase_product', [
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_purchase_validation_works(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/dashboard/purchases', [
            'user_id' => '',
            'total_amount' => 'invalid',
            'status' => 'invalid_status',
            'payment_method' => 'invalid_method',
            'transaction_id' => '',
        ]);

        $response->assertSessionHasErrors(['user_id', 'total_amount', 'status', 'payment_method', 'transaction_id']);
    }
}
