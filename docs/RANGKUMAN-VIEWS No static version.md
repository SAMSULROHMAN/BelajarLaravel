# Rangkuman Views BelajarLaravel (Kode Program Lengkap)

> **Tanggal:** 31 Aug 2026  
> **Stack:** Laravel 13.17 (PHP 8.3), Bootstrap 5.3.8, FontAwesome 6.5.2, laravel/ui 4.6  
> **Scope Dokumen:** Dokumentasi seluruh file View di `resources/views/` beserta kode program lengkap (copy verbatim)

---

## 6. Produk `resources/views/products/` (dinamis dari database)

### 6.1 `products/index.blade.php` Grid Produk + Filter + Pagination

```blade
@extends('layouts.app')

@section('title', 'Daftar Produk - ' . config('app.name', 'Laravel'))

@section('content')
<div class="container py-3 py-md-4">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item active" aria-current="page">Produk</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Daftar Produk</h1>
            <p class="text-muted small mb-0">
                @if($products->total() > 0)
                    Menampilkan {{ $products->firstItem() }}â€“{{ $products->lastItem() }} dari {{ $products->total() }} produk
                    @if(request('q') || request('category_id'))
                        <span class="ms-1">untuk
                            @if(request('q')) <span class="badge bg-primary">{{ request('q') }}â€</span> @endif
                            @if(request('category_id')) <span class="badge bg-secondary">{{ $categories->firstWhere('id', request('category_id'))->name ?? '' }}</span> @endif
                        </span>
                    @endif
                @else
                    Belum ada produk yang cocok
                @endif
            </p>
        </div>
        <button class="btn btn-outline-primary d-lg-none w-100 w-md-auto" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
            <i class="fas fa-sliders-h me-1"></i> Filter &amp; Pencarian
        </button>
    </div>

    <div class="row g-4">
        {{-- Sidebar Filter --}}
        <div class="col-lg-3">
            <div class="collapse d-lg-block" id="filterCollapse">
                <div class="card border-0 shadow-sm sticky-lg-top" style="top: 1rem;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h6 fw-bold mb-0"><i class="fas fa-filter me-1 text-primary"></i> Filter</h2>
                            @if(request('q') || request('category_id'))
                                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            @endif
                        </div>

                        <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                            {{-- Search --}}
                            <div class="mb-4">
                                <label for="q" class="form-label small fw-semibold">Cari Produk</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama produk...">
                                </div>
                            </div>

                            {{-- Category --}}
                            <div class="mb-4">
                                <label for="category_id" class="form-label small fw-semibold">Kategori</label>
                                <select id="category_id" name="category_id" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Terapkan
                                </button>
                                @if(request('q') || request('category_id'))
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Hapus Filter</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="col-lg-9">
            @if($products->isEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-box-open fa-3x text-muted opacity-50"></i>
                        </div>
                        <h5 class="fw-semibold">Produk Tidak Ditemukan</h5>
                        <p class="text-muted small mb-4">Coba ubah kata kunci atau pilih kategori lain.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-undo me-1"></i> Tampilkan Semua Produk
                        </a>
                    </div>
                </div>
            @else
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 g-md-4">
                    @foreach($products as $product)
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm product-hover overflow-hidden">
                                {{-- Image --}}
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                    <div class="ratio ratio-4x3 bg-light">
                                        <img src="{{ !$product->image ? asset('noimage.png') : asset('storage/' . $product->image) }}"
                                             class="w-100 h-100 object-fit-cover"
                                             alt="{{ $product->name }}"
                                             loading="lazy"
                                             onerror="this.src='{{ asset('noimage.png') }}'">
                                    </div>
                                </a>

                                {{-- Badge stok di atas gambar (opsional) --}}
                                @if(($product->stock ?? 0) <= 0)
                                    <span class="badge bg-danger position-absolute m-2" style="top:0; left:0;">Habis</span>
                                @elseif(($product->stock ?? 0) < 5)
                                    <span class="badge bg-warning text-dark position-absolute m-2" style="top:0; left:0;">Sisa {{ $product->stock }}</span>
                                @endif

                                <div class="card-body d-flex flex-column p-3">
                                    @if($product->category)
                                        <div class="mb-1">
                                            <span class="badge bg-light text-muted border fw-normal small">{{ $product->category->name }}</span>
                                        </div>
                                    @endif

                                    <h3 class="h6 card-title mb-1 lh-sm product-title">
                                        <a href="{{ route('products.show', $product) }}" class="text-dark text-decoration-none stretched-link-hover">
                                            {{ $product->name }}
                                        </a>
                                    </h3>

                                    @if(!empty($product->description))
                                        <p class="text-muted small mb-2 product-desc">{{ Str::limit($product->description, 60) }}</p>
                                    @endif

                                    <div class="mt-auto">
                                        <div class="fw-bold text-primary mb-2">Rp {{ number_format($product->price, 0, ',', '.') }}</div>

                                        <div class="d-grid gap-2">
                                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i> Lihat Detail
                                            </a>
                                            @if(($product->stock ?? 0) > 0)
                                                <form method="POST" action="{{ route('cart.add', $product) }}" class="d-grid">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-cart-plus me-1"></i> Keranjang
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fas fa-ban me-1"></i> Stok Habis
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .product-hover {
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .product-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.12) !important;
    }
    .product-title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.6em;
    }
    .product-desc {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.4em;
    }
    .object-fit-cover {
        object-fit: cover;
    }
    .sticky-lg-top {
        z-index: 1;
    }
    @media (max-width: 991.98px) {
        .sticky-lg-top { position: static !important; }
    }
</style>
@endpush
```

### 6.2 `products/show.blade.php` Detail Produk

```blade
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
```

---

## 7. Keranjang & Checkout

### 7.1 `resources/views/cart/index.blade.php` Keranjang (berbasis session)

```blade
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
```

### 7.2 `resources/views/checkout/create.blade.php` Konfirmasi Checkout

```blade
@extends('layouts.app')

@section('title', 'Checkout - ' . config('app.name', 'Laravel'))

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Keranjang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <h2 class="mb-4">Konfirmasi Checkout</h2>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><strong>Ringkasan Pesanan</strong></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $id => $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-center">{{ $item['quantity'] }}</td>
                                    <td class="text-end">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">Total</th>
                                    <th class="text-end">Rp {{ number_format($total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted small">Dengan menekan tombol di bawah, pesanan akan dibuat dengan status <span class="badge bg-warning text-dark">pending</span>. Stok akan diverifikasi ulang di server.</p>
                    <form method="POST" action="{{ route('checkout.store') }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg w-100"><i class="fas fa-check"></i> Buat Pesanan</button>
                    </form>
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">Kembali ke Keranjang</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 8. Pesanan `resources/views/orders/show.blade.php` (Sisi Customer)

Menampilkan detail pesanan dengan badge status, stepper proses (pending â†’ diproses â†’ dikirim â†’ selesai), aksi customer "Tandai Selesai" (modal konfirmasi), dan tabel item.

```blade
@extends('layouts.app')

@section('title', 'Pesanan #' . $order->id)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Produk</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pesanan #{{ $order->id }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Pesanan #{{ $order->id }}</h2>
        @php
            $badgeMap = ['pending'=>'warning','diproses'=>'info','dikirim'=>'primary','selesai'=>'success'];
            $badge = $badgeMap[$order->status] ?? 'secondary';
        @endphp
        <span class="badge bg-{{ $badge }} fs-6">{{ ucfirst($order->status) }}</span>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row text-muted small">
                <div class="col-md-6">ID Pesanan: <strong class="text-dark">#{{ $order->id }}</strong></div>
                <div class="col-md-6 text-md-end">Tanggal: {{ $order->created_at->format('d M Y H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- Stepper customer --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            @php $steps=['pending','diproses','dikirim','selesai']; $idx=array_search($order->status,$steps); @endphp
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                @foreach($steps as $i=>$step)
                    @php $isDone=$i<$idx; $isActive=$i===$idx; @endphp
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge rounded-pill px-3 py-2 @if($isActive) bg-primary @elseif($isDone) bg-success @else bg-light text-muted border @endif">
                            @if($isDone)<i class="fas fa-check me-1"></i>@endif {{ ucfirst($step) }}
                        </span>
                        @if($i < count($steps)-1)<i class="fas fa-chevron-right text-muted small mx-1"></i>@endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Customer action: dikirim -> selesai --}}
    @if(auth()->id() === $order->user_id)
        @if($order->status === 'dikirim')
            <div class="card border-success shadow-sm mb-4">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div>
                        <div class="fw-semibold"><i class="fas fa-box-open me-1 text-success"></i> Pesanan sudah diterima?</div>
                        <div class="small text-muted">Klik Selesai jika barang telah sampai di tujuan.</div>
                    </div>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmSelesaiModal">
                        <i class="fas fa-check me-1"></i> Tandai Selesai
                    </button>
                </div>
            </div>
        @elseif($order->status === 'selesai')
            <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> Pesanan selesai pada {{ $order->updated_at->format('d M Y H:i') }} terima kasih!</div>
        @elseif(in_array($order->status, ['pending','diproses']))
            <div class="alert alert-info small mb-4"><i class="fas fa-info-circle me-1"></i> Pesanan sedang diproses. Status akan diperbarui oleh admin menjadi Dikirim.</div>
        @endif
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Item Pesanan</div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'Produk dihapus' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada item.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end">Rp {{ number_format($order->total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali Belanja</a>
        @if(auth()->user()?->role==='admin')
            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-primary">Kelola sebagai Admin</a>
        @endif
    </div>
</div>

{{-- Modal Konfirmasi Selesai --}}
@if(auth()->id() === $order->user_id && $order->status==='dikirim')
<div class="modal fade" id="confirmSelesaiModal" tabindex="-1" aria-labelledby="confirmSelesaiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmSelesaiLabel">Konfirmasi Pesanan Selesai</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah barang pesanan <strong>#{{ $order->id }}</strong> sudah diterima dengan baik?
        <div class="small text-muted mt-2">Tindakan ini akan mengubah status menjadi <span class="badge bg-success">Selesai</span> dan tidak dapat dibatalkan.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form id="formSelesai" method="POST" action="{{ route('orders.status', $order) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="selesai">
            <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i> Ya, Tandai Selesai</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif
@endsection
```

---

## 9. Admin `resources/views/admin/`

### 9.1 `admin/products/index.blade.php` Daftar Produk (Admin)

```blade
@extends('layouts.app')

@section('title', 'Admin - Daftar Produk')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Kelola Produk</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Produk</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Kategori</th>
                        <th class="text-center" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ !$product->image ? asset('noimage.png') : asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 40px; height: 40px; object-fit: cover;" class="rounded">
                                <span>{{ $product->name }}</span>
                            </div>
                        </td>
                        <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td>
                            @if($product->stock > 0)
                                <span class="badge bg-success">{{ $product->stock }}</span>
                            @else
                                <span class="badge bg-danger">0</span>
                            @endif
                        </td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary" title="Lihat"><i class="fas fa-eye"></i></a>
                            @if(Route::has('admin.products.edit'))
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                            @endif
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada produk. <a href="{{ route('admin.products.create') }}">Tambah sekarang</a>.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($products, 'links'))
        <div class="card-footer">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
```

### 9.2 `admin/products/create.blade.php` Tambah Produk

```blade
@extends('layouts.app')

@section('title', 'Admin - Tambah Produk')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produk Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Produk</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>Tambah Produk Baru</strong></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Produk" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Harga <span class="text-danger">*</span></label>
                            <input type="number" id="price" name="price" value="{{ old('price') }}" class="form-control @error('price') is-invalid @enderror" placeholder="Harga" min="0" step="1" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Gambar</label>
                            <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Maks 2MB, format gambar.</div>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Deskripsi produk (opsional)">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stok</label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" class="form-control @error('stock') is-invalid @enderror" min="0">
                            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Produk</button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### 9.3 `admin/products/edit.blade.php` Edit Produk

```blade
@extends('layouts.app')

@section('title', 'Admin - Edit Produk')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none">Produk Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit #{{ $product->id }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Edit Produk: {{ $product->name }}</div>
                <div class="card-body p-4">
                    {{-- Preview gambar lama --}}
                    <div class="mb-4 text-center">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('noimage.png') }}"
                             alt="{{ $product->name }}"
                             class="img-thumbnail"
                             style="max-height: 180px; object-fit: cover;">
                        <div class="small text-muted mt-2">Gambar saat ini</div>
                    </div>

                    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Produk" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Harga <span class="text-danger">*</span></label>
                            <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" class="form-control @error('price') is-invalid @enderror" placeholder="Harga" min="0" step="1" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Gambar</label>
                            <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Kosongkan jika tidak ingin ganti. Maks 2MB, format gambar.</div>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Deskripsi produk (opsional)">{{ old('description', $product->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stok</label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" class="form-control @error('stock') is-invalid @enderror" min="0">
                            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary ms-auto"><i class="fas fa-eye me-1"></i> Lihat</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### 9.4 `admin/orders/index.blade.php` Daftar Pesanan (Admin)

```blade
@extends('layouts.app')

@section('title', 'Admin - Daftar Pesanan')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1"><i class="fas fa-receipt me-2 text-primary"></i>Kelola Pesanan</h2>
            <p class="text-muted small mb-0">Total {{ $orders->total() }} pesanan</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter-status" class="form-label small fw-semibold">Status</label>
                    <select id="filter-status" name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(['pending','diproses','dikirim','selesai'] as $st)
                            <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="filter-q" class="form-label small fw-semibold">Pencarian Order</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="filter-q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari #ID / Nama / Email pelanggan...">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-1"></i> Filter</button>
                    @if(request('status') || request('q'))
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:80px;">#ID</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                        <th class="text-center" style="width:220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $badgeMap = ['pending'=>'warning','diproses'=>'info','dikirim'=>'primary','selesai'=>'success'];
                            $badge = $badgeMap[$order->status] ?? 'secondary';
                            $adminFlow = ['pending'=>['diproses'],'diproses'=>['dikirim'],'dikirim'=>[],'selesai'=>[]];
                            $nextOptions = $adminFlow[$order->status] ?? [];
                        @endphp
                    <tr>
                        <td class="fw-semibold">#{{ $order->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $order->user->name ?? '-' }}</div>
                            <small class="text-muted">{{ $order->user->email ?? '-' }}</small>
                        </td>
                        <td class="small">{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td><span class="badge bg-{{ $badge }}">{{ ucfirst($order->status) }}</span></td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                @if(!empty($nextOptions))
                                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="d-flex gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm" style="width:130px;">
                                            @foreach($nextOptions as $opt)
                                                <option value="{{ $opt }}">{{ ucfirst($opt) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary" title="Ubah Status"><i class="fas fa-check"></i></button>
                                    </form>
                                @else
                                    <span class="text-muted small">
                                        @if($order->status==='selesai') Selesai @else Menunggu customer @endif
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                            Tidak ada pesanan ditemukan.
                            @if(request('status') || request('q'))
                                <a href="{{ route('admin.orders.index') }}" class="ms-1">Reset filter</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="card-footer bg-white d-flex justify-content-center">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
```

### 9.5 `admin/orders/show.blade.php` Detail Pesanan (Admin)

```blade
@extends('layouts.app')

@section('title', 'Admin - Pesanan #' . $order->id)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}" class="text-decoration-none">Kelola Pesanan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pesanan #{{ $order->id }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <h2 class="h4 fw-bold mb-0">Pesanan #{{ $order->id }}</h2>
        @php $badgeMap=['pending'=>'warning','diproses'=>'info','dikirim'=>'primary','selesai'=>'success']; $badge=$badgeMap[$order->status]??'secondary'; @endphp
        <span class="badge bg-{{ $badge }} fs-6">{{ ucfirst($order->status) }}</span>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 small">
                <div class="col-md-4">
                    <div class="text-muted">Pelanggan</div>
                    <div class="fw-semibold">{{ $order->user->name ?? '-' }}</div>
                    <div class="text-muted">{{ $order->user->email ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted">Tanggal Pesanan</div>
                    <div class="fw-semibold">{{ $order->created_at->format('d M Y H:i') }}</div>
                    <div class="text-muted">Update: {{ $order->updated_at->format('d M Y H:i') }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted">Total</div>
                    <div class="fw-bold fs-5 text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stepper --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            @php $steps=['pending','diproses','dikirim','selesai']; $idx=array_search($order->status,$steps); @endphp
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                @foreach($steps as $i=>$step)
                    @php $isDone=$i<$idx; $isActive=$i===$idx; $isFuture=$i>$idx; @endphp
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge rounded-pill px-3 py-2
                            @if($isActive) bg-primary
                            @elseif($isDone) bg-success
                            @else bg-light text-muted border @endif">
                            @if($isDone)<i class="fas fa-check me-1"></i>@endif
                            {{ ucfirst($step) }}
                        </span>
                        @if($i < count($steps)-1)
                            <i class="fas fa-chevron-right text-muted small mx-1"></i>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="small text-muted mt-2">Admin mengelola sampai <span class="badge bg-primary">Dikirim</span>, Selesai oleh customer.</div>
        </div>
    </div>

    {{-- Admin Action --}}
    <div class="card border-warning shadow-sm mb-4">
        <div class="card-header bg-warning bg-opacity-10 fw-semibold"><i class="fas fa-shield-alt me-1"></i> Panel Admin Ubah Status</div>
        <div class="card-body">
            @php $adminFlow=['pending'=>['diproses'],'diproses'=>['dikirim'],'dikirim'=>[],'selesai'=>[]]; $next=$adminFlow[$order->status]??[]; @endphp
            @if($order->status==='selesai')
                <div class="alert alert-success mb-0"><i class="fas fa-check-circle me-1"></i> Pesanan sudah selesai (oleh customer). Tidak dapat diubah lagi.</div>
            @elseif(empty($next))
                <div class="alert alert-info mb-0"><i class="fas fa-info-circle me-1"></i> Pesanan status <strong>{{ ucfirst($order->status) }}</strong> menunggu customer tandai <strong>Selesai</strong> setelah barang diterima.</div>
            @else
                <p class="small text-muted">Dari <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span> hanya bisa ke <span class="badge bg-primary">{{ ucfirst($next[0]) }}</span></p>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="d-flex gap-2 align-items-center flex-wrap">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select w-auto @error('status') is-invalid @enderror">
                        @foreach($next as $opt)
                            <option value="{{ $opt }}">{{ ucfirst($opt) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                    @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </form>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Item Pesanan</div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr><th>Produk</th><th class="text-center">Jumlah</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'Produk dihapus' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada item.</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr><th colspan="3" class="text-end">Total</th><th class="text-end">Rp {{ number_format($order->total, 0, ',', '.') }}</th></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar</a>
        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary">Lihat sebagai Customer</a>
    </div>
</div>
@endsection
```

---

## 10. Komponen `resources/views/components/product-card.blade.php`

Komponen kartu produk reusable, dipanggil dengan `@include('components.product-card', [...])`.

```blade
@php
    $id = $id ?? 1;
    $name = $name ?? 'Nama Produk';
    $price = $price ?? 'Rp 0';
    $originalPrice = $originalPrice ?? null;
    $image = $image ?? 'https://via.placeholder.com/300x200?text=No+Image';
    $rating = $rating ?? 0;
    $reviews = $reviews ?? 0;
    $badge = $badge ?? null;
    $badgeClass = $badgeClass ?? 'bg-primary';
@endphp

<div class="card product-card h-100">
    @if($badge)
    <div class="position-relative">
        <span class="badge {{ $badgeClass }} position-absolute" style="top: 10px; left: 10px; z-index: 2;">
            {{ $badge }}
        </span>
    </div>
    @endif
    
    <div class="card-img-wrapper" style="height: 200px; overflow: hidden;">
        <img src="{{ $image }}" class="card-img-top w-100 h-100" style="object-fit: cover;" alt="{{ $name }}">
    </div>
    
    <div class="card-body d-flex flex-column">
        <h6 class="card-title mb-2" style="min-height: 48px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
            {{ $name }}
        </h6>
        
        @if($rating > 0)
        <div class="mb-2">
            <div class="d-flex align-items-center">
                <div class="stars me-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($rating))
                            <i class="fas fa-star text-warning"></i>
                        @elseif($i == ceil($rating) && $rating - floor($rating) >= 0.5)
                            <i class="fas fa-star-half-alt text-warning"></i>
                        @else
                            <i class="far fa-star text-warning"></i>
                        @endif
                    @endfor
                </div>
                <small class="text-muted">({{ $reviews }})</small>
            </div>
        </div>
        @endif
        
        <div class="price-section mb-3">
            <div class="price">{{ $price }}</div>
            @if($originalPrice)
            <small class="text-muted text-decoration-line-through">{{ $originalPrice }}</small>
            @endif
        </div>
        
        <div class="mt-auto">
            <div class="row g-2">
                <div class="col-8">
                    <button class="btn btn-primary btn-sm w-100" 
                            onclick="addToCart({{ $id }}, '{{ addslashes($name) }}', '{{ $price }}', '{{ $image }}')">
                        <i class="fas fa-cart-plus"></i> Keranjang
                    </button>
                </div>
                <div class="col-4">
                    <a href="{{ url('/product/' . $id) }}" class="btn btn-outline-secondary btn-sm w-100" title="Lihat Detail">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
```

**Cara pakai:**
```blade
@include('components.product-card', [
    'id' => 1,
    'name' => 'Laptop Gaming ASUS ROG',
    'price' => 'Rp 15.000.000',
    'originalPrice' => 'Rp 18.000.000',
    'image' => 'https://via.placeholder.com/300x200?text=Laptop+Gaming',
    'rating' => 4.5,
    'reviews' => 128,
    'badge' => 'Diskon 17%',
    'badgeClass' => 'bg-danger'
])
```

---


*Dokumen ini merangkum seluruh file view (`resources/views/`) pada proyek BelajarLaravel Laravel 13.17, Bootstrap 5.3.8, FontAwesome 6.5.2, laravel/ui 4.6. Total 24 file view didokumentasikan: layout (1), halaman utama (2), autentikasi (6), area pelanggan (products 2, cart 1, checkout 1, orders 1 = 5), admin (5), komponen (1). Untuk sinkron ulang cakupan file, jalankan `php artisan view:clear` dan periksa isi `resources/views/`.*

