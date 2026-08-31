@extends('layouts.app')

@section('title', $product->name . ' - ' . config('app.name', 'Laravel'))

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h2 class="h5 mb-0">{{ $product->name }}</h2>
                </div>
                <div class="text-center mt-3 px-3">
                    <img src="{{ !$product->image ? asset('noimage.png') : asset('storage/' . $product->image) }}"
                         class="img-fluid img-thumbnail" style="max-width: 80%" alt="{{ $product->name }}">
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $product->category->name ?? 'Tanpa Kategori' }}</h5>
                    <p class="card-text">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>
                    <p class="card-text fw-bold fs-5">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <p class="card-text">
                        @if(($product->stock ?? 0) > 0)
                            <span class="badge bg-success">Stok: {{ $product->stock }}</span>
                        @else
                            <span class="badge bg-danger">Stok Habis</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('cart.add', $product) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Banyaknya</label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" id="quantity" value="{{ old('quantity', 1) }}" min="1" max="{{ $product->stock ?? 100 }}" @if(($product->stock ?? 0) <= 0) disabled @endif>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Stok tersedia: {{ $product->stock ?? 0 }}</div>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100" @if(($product->stock ?? 0) <= 0) disabled @endif>
                                <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                            </button>
                        </div>
                    </form>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Produk
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
