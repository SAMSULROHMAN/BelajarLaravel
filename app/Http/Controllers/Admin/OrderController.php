<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private const ADMIN_FLOW = [
        'pending'  => ['diproses'],
        'diproses' => ['dikirim'],
        'dikirim'  => [],
        'selesai'  => [],
    ];

    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product'])->latest();

        if ($request->filled('status') && array_key_exists($request->status, self::ADMIN_FLOW)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $kw = trim($request->q);
            $query->where(function ($q) use ($kw) {
                if (is_numeric($kw)) {
                    $q->orWhere('id', $kw);
                }
                $q->orWhereHas('user', function ($u) use ($kw) {
                    $u->where('name', 'like', "%{$kw}%")
                      ->orWhere('email', 'like', "%{$kw}%");
                });
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,dikirim,selesai',
        ]);

        // Admin tidak boleh set selesai langsung - hanya customer dari dikirim
        if ($request->status === 'selesai') {
            return back()->with('error', 'Status Selesai hanya bisa dilakukan customer setelah barang Dikirim.');
        }

        $allowed = self::ADMIN_FLOW[$order->status] ?? [];

        if (!in_array($request->status, $allowed, true)) {
            $msg = empty($allowed)
                ? "Pesanan status {$order->status} tidak dapat diubah lagi (sudah {$order->status})."
                : "Transisi tidak valid. Dari {$order->status} hanya bisa ke: " . implode(', ', $allowed) . ".";
            return back()->with('error', $msg);
        }

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan diperbarui ke ' . ucfirst($request->status) . '.');
    }
}
