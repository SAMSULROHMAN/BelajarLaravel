@extends('layouts.app')

@section('title', 'Keranjang - ' . config('app.name', 'Laravel'))

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
            <li class="breadcrumb-item active" aria-current="page">Keranjang</li>
        </ol>
    </nav>

    <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h2>

    @if(empty($items) || count($items) === 0)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h4>Keranjang Kosong</h4>
                <p class="text-muted">Belum ada produk di keranjang Anda.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary"><i class="fas fa-store"></i> Lihat Produk</a>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center" style="width: 140px;">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-center" style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $id => $item)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $item['name'] }}</div>
                                        <small class="text-muted">Rp {{ number_format($item['price'], 0, ',', '.') }} x {{ $item['quantity'] }}</small>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('cart.update', $id) }}" class="d-flex align-items-center justify-content-center gap-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm text-center" style="width: 70px;">
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Update"><i class="fas fa-check"></i></button>
                                        </form>
                                    </td>
                                    <td class="text-end fw-bold">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('cart.remove', $id) }}" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card">
                    <div class="card-header"><strong>Ringkasan</strong></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Item</span>
                            <span>{{ collect($items)->sum('quantity') }} item</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total</strong>
                            <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>
                        <a href="{{ route('checkout.create') }}" class="btn btn-success w-100 mb-2"><i class="fas fa-credit-card"></i> Lanjut Checkout</a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">Lanjut Belanja</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
