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
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h4 fw-bold mb-1">Daftar Produk</h1>
                <p class="text-muted small mb-0">
                    @if ($products->total() > 0)
                        Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }}
                        produk
                        @if (request('q') || request('category_id'))
                            <span class="ms-1">untuk
                                @if (request('q'))
                                    <span class="badge bg-primary">“{{ request('q') }}”</span>
                                @endif
                                @if (request('category_id'))
                                    <span
                                        class="badge bg-secondary">{{ $categories->firstWhere('id', request('category_id'))->name ?? '' }}</span>
                                @endif
                            </span>
                        @endif
                    @else
                        Belum ada produk yang cocok
                    @endif
                </p>
            </div>
            <button class="btn btn-outline-primary d-lg-none w-100 w-md-auto" type="button" data-bs-toggle="collapse"
                data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
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
                                @if (request('q') || request('category_id'))
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
                                        <span class="input-group-text bg-white"><i
                                                class="fas fa-search text-muted"></i></span>
                                        <input type="text" id="q" name="q" value="{{ request('q') }}"
                                            class="form-control" placeholder="Nama produk...">
                                    </div>
                                </div>

                                {{-- Category --}}
                                <div class="mb-4">
                                    <label for="category_id" class="form-label small fw-semibold">Kategori</label>
                                    <select id="category_id" name="category_id" class="form-select">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> Terapkan
                                    </button>
                                    @if (request('q') || request('category_id'))
                                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Hapus
                                            Filter</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="col-lg-9">
                @if ($products->isEmpty())
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
                        @foreach ($products as $product)
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm product-hover overflow-hidden">
                                    {{-- Image --}}
                                    <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                        <div class="ratio ratio-4x3 bg-light">
                                            <img src="{{ !$product->image ? asset('noimage.png') : asset('storage/' . $product->image) }}"
                                                class="w-100 h-100 object-fit-cover" alt="{{ $product->name }}"
                                                loading="lazy" onerror="this.src='{{ asset('noimage.png') }}'">
                                        </div>
                                    </a>

                                    {{-- Badge stok di atas gambar (opsional) --}}
                                    @if (($product->stock ?? 0) <= 0)
                                        <span class="badge bg-danger position-absolute m-2"
                                            style="top:0; left:0;">Habis</span>
                                    @elseif(($product->stock ?? 0) < 5)
                                        <span class="badge bg-warning text-dark position-absolute m-2"
                                            style="top:0; left:0;">Sisa {{ $product->stock }}</span>
                                    @endif

                                    <div class="card-body d-flex flex-column p-3">
                                        @if ($product->category)
                                            <div class="mb-1">
                                                <span
                                                    class="badge bg-light text-muted border fw-normal small">{{ $product->category->name }}</span>
                                            </div>
                                        @endif

                                        <h3 class="h6 card-title mb-1 lh-sm product-title">
                                            <a href="{{ route('products.show', $product) }}"
                                                class="text-dark text-decoration-none stretched-link-hover">
                                                {{ $product->name }}
                                            </a>
                                        </h3>

                                        @if (!empty($product->description))
                                            <p class="text-muted small mb-2 product-desc">
                                                {{ \Illuminate\Support\Str::limit($product->description, 60) }}</p>
                                        @endif

                                        <div class="mt-auto">
                                            <div class="fw-bold text-primary mb-2">Rp
                                                {{ number_format($product->price, 0, ',', '.') }}</div>

                                            <div class="d-grid gap-2">
                                                <a href="{{ route('products.show', $product) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i> Lihat Detail
                                                </a>
                                                @if (($product->stock ?? 0) > 0)
                                                    <form method="POST" action="{{ route('cart.add', $product) }}"
                                                        class="d-grid">
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
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .12) !important;
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
            .sticky-lg-top {
                position: static !important;
            }
        }
    </style>
@endpush
