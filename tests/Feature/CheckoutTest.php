<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;
    
    public function testCheckoutRejectsWhenStockInsufficient(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 2]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => [
                $product->id => ['name' => $product->name,
                    'price' => 50000, 'quantity' => 5],
            ]])
            ->post('/checkout');

        $response->assertRedirect();
        $this->assertSame(0, Order::count());
        $this->assertSame(2, $product->fresh()->stock);
    }
}
