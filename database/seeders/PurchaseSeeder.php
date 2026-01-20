<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No users or products found. Please run UserSeeder and ProductSeeder first.');
            return;
        }

        Purchase::factory(20)->create()->each(function ($purchase) use ($products) {
            $selectedProducts = $products->random(rand(1, 3));
            $totalAmount = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 5);
                $price = $product->price;
                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                $purchase->products()->attach($product->id, [
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            $purchase->update(['total_amount' => $totalAmount]);
        });
    }
}
