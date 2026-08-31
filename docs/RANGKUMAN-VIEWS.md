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

## 11. Folder `shop/` Versi Demo (Statis + localStorage)

> **Catatan penting:** File pada folder `shop/` adalah **versi demo/statis**. Datanya hardcoded (placeholder), gambar dari `via.placeholder.com`, dan keranjang memakai **`localStorage`** di sisi browser **bukan** database/session. Route `ShopController` di `routes/web.php` masih di-comment, jadi folder ini tidak dipakai saat ini (orphan). Direkomendasikan menggunakan versi dinamis (`products/`, `cart/`, `orders/`) untuk produksi.

### 11.1 `shop/index.blade.php` Beranda Toko

```blade
@extends('layouts.app')

@section('title', 'Beranda - TokoKu')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-primary text-white rounded p-5 text-center">
                <h1 class="display-4 mb-3">Selamat Datang di TokoKu</h1>
                <p class="lead mb-4">Temukan berbagai produk berkualitas dengan harga terjangkau</p>
                <a href="{{ url('/shop') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-shopping-bag"></i> Mulai Belanja
                </a>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4 text-center">Kategori Populer</h2>
            <div class="row g-4">
                <div class="col-md-2 col-6">
                    <div class="card text-center h-100 category-card">
                        <div class="card-body">
                            <i class="fas fa-laptop fa-2x text-primary mb-3"></i>
                            <h6 class="card-title">Elektronik</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="card text-center h-100 category-card">
                        <div class="card-body">
                            <i class="fas fa-tshirt fa-2x text-primary mb-3"></i>
                            <h6 class="card-title">Fashion</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="card text-center h-100 category-card">
                        <div class="card-body">
                            <i class="fas fa-home fa-2x text-primary mb-3"></i>
                            <h6 class="card-title">Rumah Tangga</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="card text-center h-100 category-card">
                        <div class="card-body">
                            <i class="fas fa-dumbbell fa-2x text-primary mb-3"></i>
                            <h6 class="card-title">Olahraga</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="card text-center h-100 category-card">
                        <div class="card-body">
                            <i class="fas fa-book fa-2x text-primary mb-3"></i>
                            <h6 class="card-title">Buku</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="card text-center h-100 category-card">
                        <div class="card-body">
                            <i class="fas fa-gamepad fa-2x text-primary mb-3"></i>
                            <h6 class="card-title">Gaming</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Products Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Produk Unggulan</h2>
                <a href="{{ url('/shop') }}" class="btn btn-outline-primary">Lihat Semua</a>
            </div>
            
            <div class="row g-4" id="featured-products">
                <!-- Sample Featured Products -->
                <div class="col-lg-3 col-md-4 col-sm-6">
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
                </div>
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 2,
                        'name' => 'Smartphone Samsung Galaxy S23',
                        'price' => 'Rp 8.500.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Samsung+S23',
                        'rating' => 4.8,
                        'reviews' => 95,
                        'badge' => 'Terlaris',
                        'badgeClass' => 'bg-success'
                    ])
                </div>
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 3,
                        'name' => 'Sepatu Nike Air Jordan',
                        'price' => 'Rp 2.800.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Nike+Jordan',
                        'rating' => 4.6,
                        'reviews' => 203,
                        'badge' => 'Terbaru',
                        'badgeClass' => 'bg-info'
                    ])
                </div>
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 4,
                        'name' => 'Kamera Canon EOS R6',
                        'price' => 'Rp 35.000.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Canon+R6',
                        'rating' => 4.9,
                        'reviews' => 67,
                        'badge' => 'Premium',
                        'badgeClass' => 'bg-warning'
                    ])
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Sale Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-gradient-danger text-white rounded p-4 mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3><i class="fas fa-bolt"></i> Flash Sale</h3>
                        <p class="mb-0">Diskon hingga 50% untuk produk pilihan. Buruan sebelum kehabisan!</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex justify-content-end align-items-center">
                            <span class="me-2">Berakhir dalam:</span>
                            <div id="countdown" class="fw-bold fs-4"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 5,
                        'name' => 'Headphone Sony WH-1000XM4',
                        'price' => 'Rp 2.500.000',
                        'originalPrice' => 'Rp 5.000.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Sony+Headphone',
                        'rating' => 4.7,
                        'reviews' => 156,
                        'badge' => 'Flash Sale 50%',
                        'badgeClass' => 'bg-danger'
                    ])
                </div>
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 6,
                        'name' => 'Smartwatch Apple Watch Series 8',
                        'price' => 'Rp 4.200.000',
                        'originalPrice' => 'Rp 6.000.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Apple+Watch',
                        'rating' => 4.8,
                        'reviews' => 89,
                        'badge' => 'Flash Sale 30%',
                        'badgeClass' => 'bg-danger'
                    ])
                </div>
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 7,
                        'name' => 'Tas Ransel Laptop Eiger',
                        'price' => 'Rp 350.000',
                        'originalPrice' => 'Rp 500.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Tas+Laptop',
                        'rating' => 4.4,
                        'reviews' => 234,
                        'badge' => 'Flash Sale 30%',
                        'badgeClass' => 'bg-danger'
                    ])
                </div>
                
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 8,
                        'name' => 'Mouse Gaming Logitech G502',
                        'price' => 'Rp 450.000',
                        'originalPrice' => 'Rp 750.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Gaming+Mouse',
                        'rating' => 4.6,
                        'reviews' => 178,
                        'badge' => 'Flash Sale 40%',
                        'badgeClass' => 'bg-danger'
                    ])
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter Section -->
    <div class="row">
        <div class="col-12">
            <div class="bg-light rounded p-5 text-center">
                <h3 class="mb-3">Berlangganan Newsletter</h3>
                <p class="mb-4">Dapatkan informasi promo dan produk terbaru langsung ke email Anda</p>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Masukkan email Anda">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-paper-plane"></i> Berlangganan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .category-card {
        cursor: pointer;
        transition: transform 0.2s;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .category-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .bg-gradient-danger {
        background: linear-gradient(45deg, #dc3545, #fd7e14);
    }
    #countdown {
        font-family: 'Courier New', monospace;
    }
</style>
@endpush

@push('scripts')
<script>
    // Countdown timer for flash sale
    function startCountdown() {
        // Set the date we're counting down to (24 hours from now)
        const countDownDate = new Date().getTime() + (24 * 60 * 60 * 1000);
        
        const timer = setInterval(function() {
            const now = new Date().getTime();
            const distance = countDownDate - now;
            
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById("countdown").innerHTML = 
                String(hours).padStart(2, '0') + ":" + 
                String(minutes).padStart(2, '0') + ":" + 
                String(seconds).padStart(2, '0');
            
            if (distance < 0) {
                clearInterval(timer);
                document.getElementById("countdown").innerHTML = "BERAKHIR";
            }
        }, 1000);
    }

    // Category card click handlers
    document.addEventListener('DOMContentLoaded', function() {
        startCountdown();
        
        const categoryCards = document.querySelectorAll('.category-card');
        categoryCards.forEach(card => {
            card.addEventListener('click', function() {
                const categoryName = this.querySelector('.card-title').textContent.toLowerCase();
                window.location.href = `/shop?category=${encodeURIComponent(categoryName)}`;
            });
        });
    });
</script>
@endpush
```

### 11.2 `shop/products.blade.php` Katalog Produk (Filter client-side)

```blade
@extends('layouts.app')

@section('title', 'Semua Produk - TokoKu')

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Produk</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h6 class="mb-0">Filter Produk</h6>
                </div>
                <div class="card-body">
                    <!-- Search -->
                    <div class="mb-4">
                        <label class="form-label">Cari Produk</label>
                        <div class="input-group">
                            <input type="text" id="search-input" class="form-control" placeholder="Nama produk...">
                            <button class="btn btn-outline-primary" onclick="applyFilters()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Categories -->
                    <div class="mb-4">
                        <label class="form-label">Kategori</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="elektronik" id="cat-elektronik">
                            <label class="form-check-label" for="cat-elektronik">
                                Elektronik (245)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="fashion" id="cat-fashion">
                            <label class="form-check-label" for="cat-fashion">
                                Fashion (189)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="rumah-tangga" id="cat-rumah">
                            <label class="form-check-label" for="cat-rumah">
                                Rumah Tangga (156)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="olahraga" id="cat-olahraga">
                            <label class="form-check-label" for="cat-olahraga">
                                Olahraga (98)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="buku" id="cat-buku">
                            <label class="form-check-label" for="cat-buku">
                                Buku & Alat Tulis (134)
                            </label>
                        </div>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="mb-4">
                        <label class="form-label">Rentang Harga</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" id="min-price" class="form-control" placeholder="Min">
                            </div>
                            <div class="col-6">
                                <input type="number" id="max-price" class="form-control" placeholder="Max">
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Harga dalam Rupiah</small>
                        </div>
                    </div>
                    
                    <!-- Rating -->
                    <div class="mb-4">
                        <label class="form-label">Rating Minimum</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rating" value="4" id="rating-4">
                            <label class="form-check-label" for="rating-4">
                                <div class="stars">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                </div>
                                4+ (50)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rating" value="3" id="rating-3">
                            <label class="form-check-label" for="rating-3">
                                <div class="stars">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                </div>
                                3+ (89)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rating" value="2" id="rating-2">
                            <label class="form-check-label" for="rating-2">
                                <div class="stars">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                </div>
                                2+ (156)
                            </label>
                        </div>
                    </div>
                    
                    <!-- Brands -->
                    <div class="mb-4">
                        <label class="form-label">Brand</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="asus" id="brand-asus">
                            <label class="form-check-label" for="brand-asus">
                                ASUS (23)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="samsung" id="brand-samsung">
                            <label class="form-check-label" for="brand-samsung">
                                Samsung (18)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="apple" id="brand-apple">
                            <label class="form-check-label" for="brand-apple">
                                Apple (15)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="nike" id="brand-nike">
                            <label class="form-check-label" for="brand-nike">
                                Nike (12)
                            </label>
                        </div>
                    </div>
                    
                    <!-- Apply Filters Button -->
                    <button class="btn btn-primary w-100 mb-2" onclick="applyFilters()">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                    <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                        <i class="fas fa-times"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9 col-md-8">
            <!-- Sort & View Options -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Semua Produk</h4>
                    <small class="text-muted">Menampilkan <span id="product-count">24</span> dari <span id="total-products">245</span> produk</small>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <!-- View Toggle -->
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-secondary active" id="grid-view" onclick="toggleView('grid')">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="btn btn-outline-secondary" id="list-view" onclick="toggleView('list')">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                    
                    <!-- Sort Options -->
                    <select class="form-select" style="width: 200px;" onchange="sortProducts(this.value)">
                        <option value="default">Urutkan</option>
                        <option value="name-asc">Nama A-Z</option>
                        <option value="name-desc">Nama Z-A</option>
                        <option value="price-asc">Harga Terendah</option>
                        <option value="price-desc">Harga Tertinggi</option>
                        <option value="rating-desc">Rating Tertinggi</option>
                        <option value="newest">Terbaru</option>
                    </select>
                </div>
            </div>
            
            <!-- Active Filters Display -->
            <div id="active-filters" class="mb-3" style="display: none;">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted">Filter aktif:</span>
                    <div id="filter-tags"></div>
                    <button class="btn btn-sm btn-outline-danger" onclick="clearFilters()">
                        <i class="fas fa-times"></i> Hapus Semua
                    </button>
                </div>
            </div>

            <!-- Products Grid -->
            <div id="products-container" class="row g-4">
                <!-- Sample Products -->
                <div class="col-lg-4 col-md-6 product-item" data-category="elektronik" data-price="15000000" data-rating="4.5">
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
                </div>
                
                <div class="col-lg-4 col-md-6 product-item" data-category="elektronik" data-price="8500000" data-rating="4.8">
                    @include('components.product-card', [
                        'id' => 2,
                        'name' => 'Smartphone Samsung Galaxy S23',
                        'price' => 'Rp 8.500.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Samsung+S23',
                        'rating' => 4.8,
                        'reviews' => 95,
                        'badge' => 'Terlaris',
                        'badgeClass' => 'bg-success'
                    ])
                </div>
                
                <div class="col-lg-4 col-md-6 product-item" data-category="fashion" data-price="2800000" data-rating="4.6">
                    @include('components.product-card', [
                        'id' => 3,
                        'name' => 'Sepatu Nike Air Jordan',
                        'price' => 'Rp 2.800.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Nike+Jordan',
                        'rating' => 4.6,
                        'reviews' => 203,
                        'badge' => 'Terbaru',
                        'badgeClass' => 'bg-info'
                    ])
                </div>
                
                <div class="col-lg-4 col-md-6 product-item" data-category="elektronik" data-price="35000000" data-rating="4.9">
                    @include('components.product-card', [
                        'id' => 4,
                        'name' => 'Kamera Canon EOS R6',
                        'price' => 'Rp 35.000.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Canon+R6',
                        'rating' => 4.9,
                        'reviews' => 67,
                        'badge' => 'Premium',
                        'badgeClass' => 'bg-warning'
                    ])
                </div>
                
                <div class="col-lg-4 col-md-6 product-item" data-category="elektronik" data-price="2500000" data-rating="4.7">
                    @include('components.product-card', [
                        'id' => 5,
                        'name' => 'Headphone Sony WH-1000XM4',
                        'price' => 'Rp 2.500.000',
                        'originalPrice' => 'Rp 5.000.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Sony+Headphone',
                        'rating' => 4.7,
                        'reviews' => 156,
                        'badge' => 'Flash Sale 50%',
                        'badgeClass' => 'bg-danger'
                    ])
                </div>
                
                <div class="col-lg-4 col-md-6 product-item" data-category="elektronik" data-price="4200000" data-rating="4.8">
                    @include('components.product-card', [
                        'id' => 6,
                        'name' => 'Smartwatch Apple Watch Series 8',
                        'price' => 'Rp 4.200.000',
                        'originalPrice' => 'Rp 6.000.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Apple+Watch',
                        'rating' => 4.8,
                        'reviews' => 89,
                        'badge' => 'Flash Sale 30%',
                        'badgeClass' => 'bg-danger'
                    ])
                </div>
                
                <div class="col-lg-4 col-md-6 product-item" data-category="fashion" data-price="350000" data-rating="4.4">
                    @include('components.product-card', [
                        'id' => 7,
                        'name' => 'Tas Ransel Laptop Eiger',
                        'price' => 'Rp 350.000',
                        'originalPrice' => 'Rp 500.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Tas+Laptop',
                        'rating' => 4.4,
                        'reviews' => 234,
                        'badge' => 'Flash Sale 30%',
                        'badgeClass' => 'bg-danger'
                    ])
                </div>
                
                <div class="col-lg-4 col-md-6 product-item" data-category="elektronik" data-price="450000" data-rating="4.6">
                    @include('components.product-card', [
                        'id' => 8,
                        'name' => 'Mouse Gaming Logitech G502',
                        'price' => 'Rp 450.000',
                        'originalPrice' => 'Rp 750.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Gaming+Mouse',
                        'rating' => 4.6,
                        'reviews' => 178,
                        'badge' => 'Flash Sale 40%',
                        'badgeClass' => 'bg-danger'
                    ])
                </div>
                
                <div class="col-lg-4 col-md-6 product-item" data-category="rumah-tangga" data-price="1500000" data-rating="4.3">
                    @include('components.product-card', [
                        'id' => 17,
                        'name' => 'Rice Cooker Digital Miyako',
                        'price' => 'Rp 1.500.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Rice+Cooker',
                        'rating' => 4.3,
                        'reviews' => 156
                    ])
                </div>
                
                <div class="col-lg-4 col-md-6 product-item" data-category="olahraga" data-price="850000" data-rating="4.5">
                    @include('components.product-card', [
                        'id' => 18,
                        'name' => 'Dumbbell Set 20kg',
                        'price' => 'Rp 850.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Dumbbell',
                        'rating' => 4.5,
                        'reviews' => 89
                    ])
                </div>
                
                <div class="col-lg-4 col-md-6 product-item" data-category="buku" data-price="125000" data-rating="4.7">
                    @include('components.product-card', [
                        'id' => 19,
                        'name' => 'Buku Programming Laravel',
                        'price' => 'Rp 125.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Buku+Laravel',
                        'rating' => 4.7,
                        'reviews' => 234
                    ])
                </div>
                
                <div class="col-lg-4 col-md-6 product-item" data-category="fashion" data-price="450000" data-rating="4.2">
                    @include('components.product-card', [
                        'id' => 20,
                        'name' => 'Kaos Polos Premium Cotton',
                        'price' => 'Rp 450.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Kaos+Premium',
                        'rating' => 4.2,
                        'reviews' => 67
                    ])
                </div>
            </div>
            
            <!-- No Results -->
            <div id="no-results" class="text-center py-5" style="display: none;">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>Produk Tidak Ditemukan</h4>
                <p class="text-muted">Coba ubah kata kunci pencarian atau filter Anda</p>
            </div>
            
            <!-- Pagination -->
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <span class="page-link">Sebelumnya</span>
                    </li>
                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <span class="page-link">...</span>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">10</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">Selanjutnya</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentView = 'grid';
    
    function toggleView(view) {
        currentView = view;
        const gridBtn = document.getElementById('grid-view');
        const listBtn = document.getElementById('list-view');
        const container = document.getElementById('products-container');
        
        if (view === 'grid') {
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            container.className = 'row g-4';
            
            // Reset grid layout for product items
            document.querySelectorAll('.product-item').forEach(item => {
                item.className = 'col-lg-4 col-md-6 product-item';
            });
        } else {
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
            container.className = 'row g-3';
            
            // Set list layout for product items
            document.querySelectorAll('.product-item').forEach(item => {
                item.className = 'col-12 product-item';
            });
        }
    }
    
    function applyFilters() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const minPrice = parseInt(document.getElementById('min-price').value) || 0;
        const maxPrice = parseInt(document.getElementById('max-price').value) || Infinity;
        const selectedCategories = Array.from(document.querySelectorAll('input[id^="cat-"]:checked')).map(cb => cb.value);
        const selectedBrands = Array.from(document.querySelectorAll('input[id^="brand-"]:checked')).map(cb => cb.value);
        const selectedRating = document.querySelector('input[name="rating"]:checked')?.value;
        
        const products = document.querySelectorAll('.product-item');
        let visibleCount = 0;
        
        products.forEach(product => {
            let show = true;
            
            // Search filter
            if (searchTerm) {
                const productName = product.querySelector('.card-title').textContent.toLowerCase();
                if (!productName.includes(searchTerm)) {
                    show = false;
                }
            }
            
            // Category filter
            if (selectedCategories.length > 0) {
                const productCategory = product.dataset.category;
                if (!selectedCategories.includes(productCategory)) {
                    show = false;
                }
            }
            
            // Price filter
            const productPrice = parseInt(product.dataset.price);
            if (productPrice < minPrice || productPrice > maxPrice) {
                show = false;
            }
            
            // Rating filter
            if (selectedRating) {
                const productRating = parseFloat(product.dataset.rating);
                if (productRating < parseInt(selectedRating)) {
                    show = false;
                }
            }
            
            if (show) {
                product.style.display = 'block';
                visibleCount++;
            } else {
                product.style.display = 'none';
            }
        });
        
        // Update product count
        document.getElementById('product-count').textContent = visibleCount;
        
        // Show/hide no results message
        const noResults = document.getElementById('no-results');
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
        
        // Update active filters display
        updateActiveFilters();
    }
    
    function updateActiveFilters() {
        const filterTags = document.getElementById('filter-tags');
        const activeFiltersDiv = document.getElementById('active-filters');
        const searchTerm = document.getElementById('search-input').value;
        const minPrice = document.getElementById('min-price').value;
        const maxPrice = document.getElementById('max-price').value;
        const selectedCategories = Array.from(document.querySelectorAll('input[id^="cat-"]:checked'));
        const selectedBrands = Array.from(document.querySelectorAll('input[id^="brand-"]:checked'));
        const selectedRating = document.querySelector('input[name="rating"]:checked');
        
        let tags = [];
        
        if (searchTerm) tags.push(`Pencarian: "${searchTerm}"`);
        if (minPrice || maxPrice) {
            tags.push(`Harga: ${minPrice || '0'} - ${maxPrice || 'âˆž'}`);
        }
        
        selectedCategories.forEach(cb => {
            tags.push(`Kategori: ${cb.labels[0].textContent.split('(')[0].trim()}`);
        });
        
        selectedBrands.forEach(cb => {
            tags.push(`Brand: ${cb.labels[0].textContent.split('(')[0].trim()}`);
        });
        
        if (selectedRating) {
            tags.push(`Rating: ${selectedRating.value}+ bintang`);
        }
        
        if (tags.length > 0) {
            filterTags.innerHTML = tags.map(tag => 
                `<span class="badge bg-primary">${tag}</span>`
            ).join(' ');
            activeFiltersDiv.style.display = 'block';
        } else {
            activeFiltersDiv.style.display = 'none';
        }
    }
    
    function clearFilters() {
        // Clear all form inputs
        document.getElementById('search-input').value = '';
        document.getElementById('min-price').value = '';
        document.getElementById('max-price').value = '';
        
        // Uncheck all checkboxes and radio buttons
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        document.querySelectorAll('input[type="radio"]').forEach(rb => rb.checked = false);
        
        // Reset sort
        document.querySelector('select').selectedIndex = 0;
        
        // Show all products
        document.querySelectorAll('.product-item').forEach(product => {
            product.style.display = 'block';
        });
        
        // Update counts
        document.getElementById('product-count').textContent = '12';
        document.getElementById('no-results').style.display = 'none';
        document.getElementById('active-filters').style.display = 'none';
    }
    
    function sortProducts(sortBy) {
        const container = document.getElementById('products-container');
        const products = Array.from(container.querySelectorAll('.product-item:not([style*="display: none"])'));
        
        products.sort((a, b) => {
            switch(sortBy) {
                case 'name-asc':
                    return a.querySelector('.card-title').textContent.localeCompare(b.querySelector('.card-title').textContent);
                case 'name-desc':
                    return b.querySelector('.card-title').textContent.localeCompare(a.querySelector('.card-title').textContent);
                case 'price-asc':
                    return parseInt(a.dataset.price) - parseInt(b.dataset.price);
                case 'price-desc':
                    return parseInt(b.dataset.price) - parseInt(a.dataset.price);
                case 'rating-desc':
                    return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                default:
                    return 0;
            }
        });
        
        // Reorder in DOM
        products.forEach(product => container.appendChild(product));
    }
    
    // Load URL parameters on page load
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');
        
        if (category) {
            const categoryCheckbox = document.getElementById(`cat-${category}`);
            if (categoryCheckbox) {
                categoryCheckbox.checked = true;
                applyFilters();
            }
        }
    });
</script>
@endpush
```

### 11.3 `shop/product.blade.php` Detail Produk (Demo)

```blade
@extends('layouts.app')

@section('title', 'Detail Produk - TokoKu')

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/shop') }}">Produk</a></li>
            <li class="breadcrumb-item active">Detail Produk</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="main-image mb-3">
                    <img id="mainImage" src="https://via.placeholder.com/600x400?text=Laptop+Gaming+ASUS+ROG" 
                         class="card-img-top w-100" style="height: 400px; object-fit: cover;" alt="Product Image">
                </div>
                
                <!-- Thumbnail Images -->
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-3">
                            <img src="https://via.placeholder.com/150x100?text=Image+1" 
                                 class="img-thumbnail w-100 thumbnail-img active" 
                                 onclick="changeMainImage(this.src)" 
                                 style="cursor: pointer; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col-3">
                            <img src="https://via.placeholder.com/150x100?text=Image+2" 
                                 class="img-thumbnail w-100 thumbnail-img" 
                                 onclick="changeMainImage(this.src)" 
                                 style="cursor: pointer; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col-3">
                            <img src="https://via.placeholder.com/150x100?text=Image+3" 
                                 class="img-thumbnail w-100 thumbnail-img" 
                                 onclick="changeMainImage(this.src)" 
                                 style="cursor: pointer; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col-3">
                            <img src="https://via.placeholder.com/150x100?text=Image+4" 
                                 class="img-thumbnail w-100 thumbnail-img" 
                                 onclick="changeMainImage(this.src)" 
                                 style="cursor: pointer; height: 80px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <div class="product-details">
                <!-- Product Title & Rating -->
                <h1 class="h3 mb-3">Laptop Gaming ASUS ROG Strix G15</h1>
                
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="stars me-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                        </div>
                        <span class="text-muted">4.5 (128 ulasan)</span>
                    </div>
                    
                    <div class="mb-2">
                        <span class="badge bg-success me-2">Stok Tersedia</span>
                        <span class="text-muted">Terjual 500+</span>
                    </div>
                </div>

                <!-- Price -->
                <div class="price-section mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <h3 class="price text-success mb-0 me-3">Rp 15.000.000</h3>
                        <span class="text-muted text-decoration-line-through">Rp 18.000.000</span>
                    </div>
                    <div class="badge bg-danger">Hemat Rp 3.000.000 (17%)</div>
                </div>

                <!-- Specifications Preview -->
                <div class="mb-4">
                    <h6>Spesifikasi Utama:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-microchip text-primary me-2"></i> AMD Ryzen 7 6800H</li>
                        <li><i class="fas fa-memory text-primary me-2"></i> RAM 16GB DDR5</li>
                        <li><i class="fas fa-hdd text-primary me-2"></i> SSD 512GB NVMe</li>
                        <li><i class="fas fa-desktop text-primary me-2"></i> RTX 3070 8GB</li>
                        <li><i class="fas fa-tv text-primary me-2"></i> 15.6" FHD 144Hz</li>
                    </ul>
                </div>

                <!-- Quantity Selector -->
                <div class="mb-4">
                    <label class="form-label">Jumlah:</label>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary" onclick="changeQuantity(-1)">-</button>
                        <input type="number" id="quantity" class="form-control mx-2 text-center" value="1" min="1" max="10" style="width: 80px;">
                        <button class="btn btn-outline-secondary" onclick="changeQuantity(1)">+</button>
                        <span class="ms-3 text-muted">Stok: 25 unit</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mb-4">
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-primary btn-lg w-100" onclick="addToCartWithQuantity()">
                                <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-success btn-lg w-100">
                                <i class="fas fa-bolt"></i> Beli Sekarang
                            </button>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <button class="btn btn-outline-danger w-100">
                                <i class="fas fa-heart"></i> Wishlist
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#shareModal">
                                <i class="fas fa-share"></i> Bagikan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Seller Info -->
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Informasi Penjual</h6>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img src="https://via.placeholder.com/50x50?text=Store" class="rounded-circle" alt="Store">
                            </div>
                            <div class="col">
                                <strong>TechStore Official</strong>
                                <div class="text-muted small">
                                    <i class="fas fa-star text-warning"></i> 4.8 | 10k+ produk
                                </div>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-store"></i> Kunjungi Toko
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button">
                        Deskripsi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button">
                        Spesifikasi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                        Ulasan (128)
                    </button>
                </li>
            </ul>
            
            <div class="tab-content mt-4" id="productTabContent">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description">
                    <div class="card">
                        <div class="card-body">
                            <h5>Deskripsi Produk</h5>
                            <p>ASUS ROG Strix G15 adalah laptop gaming yang dirancang khusus untuk para gamer dan content creator yang membutuhkan performa tinggi. Ditenagai oleh processor AMD Ryzen 7 6800H dan kartu grafis NVIDIA GeForce RTX 3070, laptop ini mampu menjalankan game-game terbaru dengan setting maksimal.</p>
                            
                            <h6>Fitur Unggulan:</h6>
                            <ul>
                                <li><strong>Performa Gaming Maksimal:</strong> RTX 3070 dengan 8GB VRAM untuk gaming 1440p</li>
                                <li><strong>Layar Gaming:</strong> 15.6" FHD dengan refresh rate 144Hz untuk gameplay yang smooth</li>
                                <li><strong>Cooling System:</strong> ROG Intelligent Cooling dengan dual fan untuk suhu optimal</li>
                                <li><strong>Keyboard Gaming:</strong> RGB backlit keyboard dengan anti-ghosting</li>
                                <li><strong>Audio Immersive:</strong> Dolby Atmos untuk pengalaman audio yang menakjubkan</li>
                                <li><strong>Konektivitas Lengkap:</strong> USB-C, HDMI, Ethernet, dan WiFi 6</li>
                            </ul>
                            
                            <p>Cocok untuk gaming, streaming, editing video, dan multitasking berat. Garansi resmi 2 tahun.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Specifications Tab -->
                <div class="tab-pane fade" id="specifications">
                    <div class="card">
                        <div class="card-body">
                            <h5>Spesifikasi Lengkap</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr><td><strong>Processor</strong></td><td>AMD Ryzen 7 6800H (8-core, 16-thread)</td></tr>
                                        <tr><td><strong>RAM</strong></td><td>16GB DDR5-4800</td></tr>
                                        <tr><td><strong>Storage</strong></td><td>512GB PCIe 4.0 NVMe SSD</td></tr>
                                        <tr><td><strong>Graphics</strong></td><td>NVIDIA GeForce RTX 3070 8GB GDDR6</td></tr>
                                        <tr><td><strong>Display</strong></td><td>15.6" FHD (1920x1080) 144Hz IPS</td></tr>
                                        <tr><td><strong>OS</strong></td><td>Windows 11 Home</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-striped">
                                        <tr><td><strong>Keyboard</strong></td><td>RGB Backlit, Anti-Ghosting</td></tr>
                                        <tr><td><strong>Audio</strong></td><td>Dolby Atmos, Dual Speakers</td></tr>
                                        <tr><td><strong>Webcam</strong></td><td>720p HD</td></tr>
                                        <tr><td><strong>Battery</strong></td><td>90Wh, Fast Charging</td></tr>
                                        <tr><td><strong>Weight</strong></td><td>2.3 kg</td></tr>
                                        <tr><td><strong>Dimensions</strong></td><td>35.4 x 25.9 x 2.7 cm</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews">
                    <div class="card">
                        <div class="card-body">
                            <h5>Ulasan Pelanggan</h5>
                            
                            <!-- Review Summary -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <h2 class="display-4 text-warning">4.5</h2>
                                        <div class="stars mb-2">
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                        </div>
                                        <p class="text-muted">128 ulasan</p>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="rating-breakdown">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2">5â˜…</span>
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: 65%"></div>
                                            </div>
                                            <span class="small text-muted">83</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2">4â˜…</span>
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: 20%"></div>
                                            </div>
                                            <span class="small text-muted">26</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2">3â˜…</span>
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: 10%"></div>
                                            </div>
                                            <span class="small text-muted">13</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2">2â˜…</span>
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: 3%"></div>
                                            </div>
                                            <span class="small text-muted">4</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">1â˜…</span>
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: 2%"></div>
                                            </div>
                                            <span class="small text-muted">2</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Individual Reviews -->
                            <div class="reviews-list">
                                <!-- Review 1 -->
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex align-items-start">
                                        <img src="https://via.placeholder.com/40x40?text=U1" class="rounded-circle me-3" alt="User">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <strong>Ahmad Wijaya</strong>
                                                    <div class="stars">
                                                        <i class="fas fa-star text-warning"></i>
                                                        <i class="fas fa-star text-warning"></i>
                                                        <i class="fas fa-star text-warning"></i>
                                                        <i class="fas fa-star text-warning"></i>
                                                        <i class="fas fa-star text-warning"></i>
                                                    </div>
                                                </div>
                                                <small class="text-muted">2 hari lalu</small>
                                            </div>
                                            <p>Laptop gaming yang luar biasa! Performa sangat memuaskan untuk gaming dan editing. Cooling system bekerja dengan baik, tidak overheat saat gaming berat. Highly recommended!</p>
                                            <div class="d-flex">
                                                <button class="btn btn-sm btn-outline-primary me-2">ðŸ‘ Berguna (12)</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Review 2 -->
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex align-items-start">
                                        <img src="https://via.placeholder.com/40x40?text=U2" class="rounded-circle me-3" alt="User">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <strong>Sarah Putri</strong>
                                                    <div class="stars">
                                                        <i class="fas fa-star text-warning"></i>
                                                        <i class="fas fa-star text-warning"></i>
                                                        <i class="fas fa-star text-warning"></i>
                                                        <i class="fas fa-star text-warning"></i>
                                                        <i class="far fa-star text-warning"></i>
                                                    </div>
                                                </div>
                                                <small class="text-muted">1 minggu lalu</small>
                                            </div>
                                            <p>Build quality bagus, layar 144Hz membuat gaming jadi smooth. Keyboard nyaman untuk typing. Hanya saja baterai cukup boros untuk usage non-gaming.</p>
                                            <div class="d-flex">
                                                <button class="btn btn-sm btn-outline-primary me-2">ðŸ‘ Berguna (8)</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button class="btn btn-outline-primary">Lihat Semua Ulasan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Produk Serupa</h3>
            <div class="row g-4">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 9,
                        'name' => 'Laptop Gaming MSI Katana GF66',
                        'price' => 'Rp 13.500.000',
                        'image' => 'https://via.placeholder.com/300x200?text=MSI+Katana',
                        'rating' => 4.3,
                        'reviews' => 89
                    ])
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 10,
                        'name' => 'Laptop Gaming Acer Predator Helios',
                        'price' => 'Rp 16.200.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Acer+Predator',
                        'rating' => 4.4,
                        'reviews' => 156
                    ])
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 11,
                        'name' => 'Laptop Gaming HP Omen 16',
                        'price' => 'Rp 14.800.000',
                        'image' => 'https://via.placeholder.com/300x200?text=HP+Omen',
                        'rating' => 4.2,
                        'reviews' => 72
                    ])
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 12,
                        'name' => 'Laptop Gaming Lenovo Legion 5',
                        'price' => 'Rp 12.900.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Lenovo+Legion',
                        'rating' => 4.6,
                        'reviews' => 134
                    ])
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bagikan Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary">
                        <i class="fab fa-facebook"></i> Bagikan ke Facebook
                    </button>
                    <button class="btn btn-info">
                        <i class="fab fa-twitter"></i> Tweet
                    </button>
                    <button class="btn btn-success">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </button>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ url()->current() }}" readonly>
                        <button class="btn btn-outline-secondary" onclick="copyToClipboard()">Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .thumbnail-img.active {
        border: 2px solid #007bff !important;
    }
    .stars i {
        font-size: 14px;
    }
</style>
@endpush

@push('scripts')
<script>
    function changeMainImage(src) {
        document.getElementById('mainImage').src = src;
        
        // Remove active class from all thumbnails
        document.querySelectorAll('.thumbnail-img').forEach(img => {
            img.classList.remove('active');
        });
        
        // Add active class to clicked thumbnail
        event.target.classList.add('active');
    }
    
    function changeQuantity(delta) {
        const quantityInput = document.getElementById('quantity');
        let currentValue = parseInt(quantityInput.value);
        let newValue = currentValue + delta;
        
        if (newValue >= 1 && newValue <= 10) {
            quantityInput.value = newValue;
        }
    }
    
    function addToCartWithQuantity() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const productName = 'Laptop Gaming ASUS ROG Strix G15';
        const productPrice = 'Rp 15.000.000';
        const productImage = 'https://via.placeholder.com/600x400?text=Laptop+Gaming+ASUS+ROG';
        
        // Get existing cart
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let existingItem = cart.find(item => item.id === 1);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                id: 1,
                name: productName,
                price: productPrice,
                image: productImage,
                quantity: quantity
            });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        
        showAlert(`${quantity} item berhasil ditambahkan ke keranjang!`, 'success');
    }
    
    function copyToClipboard() {
        const input = document.querySelector('#shareModal input');
        input.select();
        document.execCommand('copy');
        showAlert('Link berhasil disalin!', 'success');
    }
</script>
@endpush
```

### 11.4 `shop/cart.blade.php` Keranjang Belanja (Demo, localStorage)

```blade
@extends('layouts.app')

@section('title', 'Keranjang Belanja - TokoKu')

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Keranjang Belanja</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Cart Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Keranjang Belanja</h2>
                <button class="btn btn-outline-danger btn-sm" onclick="clearCart()">
                    <i class="fas fa-trash"></i> Kosongkan Keranjang
                </button>
            </div>

            <!-- Cart Items -->
            <div id="cart-items">
                <div class="card mb-3" id="empty-cart" style="display: none;">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h4>Keranjang Belanja Kosong</h4>
                        <p class="text-muted">Belum ada produk dalam keranjang belanja Anda</p>
                        <a href="{{ url('/shop') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Mulai Belanja
                        </a>
                    </div>
                </div>
            </div>

            <!-- Continue Shopping -->
            <div class="mt-4">
                <a href="{{ url('/shop') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Lanjutkan Belanja
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal (<span id="total-items">0</span> item)</span>
                        <span id="subtotal">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ongkos Kirim</span>
                        <span id="shipping-cost">Rp 25.000</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Diskon</span>
                        <span id="discount" class="text-success">-Rp 0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total</strong>
                        <strong id="total-amount">Rp 0</strong>
                    </div>
                    
                    <!-- Promo Code -->
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" id="promo-code" class="form-control" placeholder="Kode promo">
                            <button class="btn btn-outline-secondary" onclick="applyPromo()">Gunakan</button>
                        </div>
                    </div>
                    
                    <!-- Shipping Options -->
                    <div class="mb-3">
                        <label class="form-label">Pilih Pengiriman:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="shipping" id="regular" value="25000" checked onchange="updateShipping()">
                            <label class="form-check-label" for="regular">
                                Reguler (3-5 hari) - Rp 25.000
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="shipping" id="express" value="50000" onchange="updateShipping()">
                            <label class="form-check-label" for="express">
                                Express (1-2 hari) - Rp 50.000
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="shipping" id="same-day" value="100000" onchange="updateShipping()">
                            <label class="form-check-label" for="same-day">
                                Same Day - Rp 100.000
                            </label>
                        </div>
                    </div>
                    
                    <button class="btn btn-success btn-lg w-100 mb-2" onclick="proceedToCheckout()" id="checkout-btn" disabled>
                        <i class="fas fa-credit-card"></i> Lanjut ke Pembayaran
                    </button>
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt"></i> Pembayaran 100% Aman
                        </small>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Metode Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <img src="https://via.placeholder.com/60x30?text=VISA" class="img-fluid" alt="Visa">
                        </div>
                        <div class="col-4">
                            <img src="https://via.placeholder.com/60x30?text=MC" class="img-fluid" alt="Mastercard">
                        </div>
                        <div class="col-4">
                            <img src="https://via.placeholder.com/60x30?text=BCA" class="img-fluid" alt="BCA">
                        </div>
                        <div class="col-4">
                            <img src="https://via.placeholder.com/60x30?text=OVO" class="img-fluid" alt="OVO">
                        </div>
                        <div class="col-4">
                            <img src="https://via.placeholder.com/60x30?text=DANA" class="img-fluid" alt="DANA">
                        </div>
                        <div class="col-4">
                            <img src="https://via.placeholder.com/60x30?text=GOPAY" class="img-fluid" alt="GoPay">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recently Viewed -->
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="mb-4">Produk yang Baru Dilihat</h4>
            <div class="row g-4">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 13,
                        'name' => 'Mouse Gaming RGB Pro',
                        'price' => 'Rp 450.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Gaming+Mouse',
                        'rating' => 4.4,
                        'reviews' => 67
                    ])
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 14,
                        'name' => 'Keyboard Mechanical RGB',
                        'price' => 'Rp 850.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Keyboard',
                        'rating' => 4.7,
                        'reviews' => 134
                    ])
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 15,
                        'name' => 'Headset Gaming 7.1',
                        'price' => 'Rp 650.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Headset',
                        'rating' => 4.3,
                        'reviews' => 89
                    ])
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.product-card', [
                        'id' => 16,
                        'name' => 'Webcam HD 1080p',
                        'price' => 'Rp 350.000',
                        'image' => 'https://via.placeholder.com/300x200?text=Webcam',
                        'rating' => 4.2,
                        'reviews' => 45
                    ])
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Remove Item Modal -->
<div class="modal fade" id="removeItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus item ini dari keranjang?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmRemoveItem()">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentRemoveId = null;
    
    function loadCart() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const cartItemsContainer = document.getElementById('cart-items');
        const emptyCart = document.getElementById('empty-cart');
        
        if (cart.length === 0) {
            emptyCart.style.display = 'block';
            document.getElementById('checkout-btn').disabled = true;
        } else {
            emptyCart.style.display = 'none';
            document.getElementById('checkout-btn').disabled = false;
            
            // Remove existing cart items (except empty cart message)
            const existingItems = cartItemsContainer.querySelectorAll('.cart-item');
            existingItems.forEach(item => item.remove());
            
            cart.forEach(item => {
                const cartItemHtml = `
                    <div class="card mb-3 cart-item">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="${item.image}" class="img-fluid rounded" alt="${item.name}" style="height: 80px; object-fit: cover;">
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-1">${item.name}</h6>
                                    <small class="text-muted">Stok tersedia</small>
                                </div>
                                <div class="col-md-2">
                                    <div class="fw-bold">${item.price}</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-secondary" onclick="updateQuantity(${item.id}, -1)">-</button>
                                        <input type="text" class="form-control text-center" value="${item.quantity}" readonly>
                                        <button class="btn btn-outline-secondary" onclick="updateQuantity(${item.id}, 1)">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button class="btn btn-sm btn-outline-danger" onclick="removeItem(${item.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <div class="mt-1">
                                        <small class="text-muted">Simpan untuk nanti</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                cartItemsContainer.insertAdjacentHTML('beforeend', cartItemHtml);
            });
        }
        
        updateSummary();
    }
    
    function updateQuantity(id, change) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const item = cart.find(item => item.id === id);
        
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                cart = cart.filter(item => item.id !== id);
            }
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
            updateCartCount();
        }
    }
    
    function removeItem(id) {
        currentRemoveId = id;
        new bootstrap.Modal(document.getElementById('removeItemModal')).show();
    }
    
    function confirmRemoveItem() {
        if (currentRemoveId) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart = cart.filter(item => item.id !== currentRemoveId);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
            updateCartCount();
            bootstrap.Modal.getInstance(document.getElementById('removeItemModal')).hide();
            currentRemoveId = null;
        }
    }
    
    function clearCart() {
        if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
            localStorage.removeItem('cart');
            loadCart();
            updateCartCount();
        }
    }
    
    function updateSummary() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        let subtotal = 0;
        let totalItems = 0;
        
        cart.forEach(item => {
            const price = parseInt(item.price.replace(/[^\d]/g, ''));
            subtotal += price * item.quantity;
            totalItems += item.quantity;
        });
        
        const shippingCost = parseInt(document.querySelector('input[name="shipping"]:checked').value);
        const discount = 0; // Can be calculated based on promo code
        const total = subtotal + shippingCost - discount;
        
        document.getElementById('total-items').textContent = totalItems;
        document.getElementById('subtotal').textContent = formatCurrency(subtotal);
        document.getElementById('shipping-cost').textContent = formatCurrency(shippingCost);
        document.getElementById('discount').textContent = '-' + formatCurrency(discount);
        document.getElementById('total-amount').textContent = formatCurrency(total);
    }
    
    function updateShipping() {
        updateSummary();
    }
    
    function applyPromo() {
        const promoCode = document.getElementById('promo-code').value.trim();
        
        if (promoCode === 'DISKON10') {
            showAlert('Kode promo berhasil diterapkan! Diskon 10%', 'success');
            // Apply 10% discount logic here
        } else if (promoCode === 'GRATIS50K') {
            showAlert('Kode promo berhasil diterapkan! Gratis ongkir Rp 50.000', 'success');
            // Apply free shipping logic here
        } else if (promoCode !== '') {
            showAlert('Kode promo tidak valid', 'error');
        }
        
        updateSummary();
    }
    
    function proceedToCheckout() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        if (cart.length === 0) {
            showAlert('Keranjang belanja kosong', 'error');
            return;
        }
        
        // Simulate checkout process
        showAlert('Mengarahkan ke halaman pembayaran...', 'info');
        setTimeout(() => {
            showAlert('Demo: Checkout berhasil! Terima kasih atas pesanan Anda.', 'success');
            // In real app, redirect to payment page
        }, 2000);
    }
    
    function formatCurrency(amount) {
        return 'Rp ' + amount.toLocaleString('id-ID');
    }
    
    // Load cart on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadCart();
    });
</script>
@endpush
```

---

*Dokumen ini merangkum seluruh file view (`resources/views/`) pada proyek BelajarLaravel Laravel 13.17, Bootstrap 5.3.8, FontAwesome 6.5.2, laravel/ui 4.6. Total 24 file view didokumentasikan: layout (1), halaman utama (2), autentikasi (6), area pelanggan (products 2, cart 1, checkout 1, orders 1 = 5), admin (5), komponen (1), dan folder shop demo (4). Semua kode disalin verbatim. Untuk sinkron ulang cakupan file, jalankan `php artisan view:clear` dan periksa isi `resources/views/`.*

