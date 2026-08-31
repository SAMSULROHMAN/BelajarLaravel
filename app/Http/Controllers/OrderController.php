<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    
    public function show(Order $order)
    {
        abort_unless(
            $order->user_id === auth()->id()
                || auth()->user()?->role === 'admin',
            403
        );

        return view('orders.show', [
            'order' => $order->load(['user', 'items.product']),
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $request->validate([
            'status' => 'required|in:selesai',
        ]);

        if ($order->status !== 'dikirim') {
            return back()->with('error', 'Hanya pesanan dengan status Dikirim yang bisa ditandai Selesai.');
        }

        $order->update(['status' => 'selesai']);

        return back()->with('success', 'Terima kasih, pesanan ditandai Selesai.');
    }

}
