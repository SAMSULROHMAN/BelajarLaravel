<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function create()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang masih kosong.');
        }

        $total = collect($cart)
            ->sum(fn ($item) => $item['price'] * $item['quantity']);

        return view('checkout.create', compact('cart', 'total'));
    }
    public function store()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Keranjang masih kosong.');
        }

        $products = Product::whereIn('id', array_keys($cart))
            ->get()->keyBy('id');

        foreach ($cart as $productId => $item) {
            $product = $products->get($productId);
            if (!$product || $product->stock < $item['quantity']) {
                return back()->with('error',
                    "Stok \"{$item['name']}\" tidak cukup.");
            }
        }

        // lanjut ke proses transaksi...

        $order = DB::transaction(function () use ($cart, $products) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => collect($cart)
                    ->sum(fn ($i) => $i['price'] * $i['quantity']),
                'status' => 'pending',
            ]);

            foreach ($cart as $productId => $item) {
                $order->items()->create([
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
                $products->get($productId)
                    ->decrement('stock', $item['quantity']);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat.');
    }
}
