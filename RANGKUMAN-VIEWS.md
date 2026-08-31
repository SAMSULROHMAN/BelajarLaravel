# Rangkuman File Views — BelajarLaravel

Dokumen ini merangkum seluruh file **View** yang berada di folder `resources/views/` pada proyek Laravel **BelajarLaravel** (sebuah aplikasi toko online), beserta contoh kode untuk masing-masing kelompok.

---

## 1. Pendahuluan

### 1.1 Teknologi & Library yang Digunakan

| Komponen | Keterangan |
|----------|------------|
| **Blade** | Template engine bawaan Laravel |
| **Bootstrap 5.3.8** | Framework CSS/JS (via CDN) |
| **FontAwesome 6.5.2** | Ikon (via CDN) |
| **Nunito** | Font (via Bunny CDN) |
| **jQuery** | Tidak dipakai; memakai `bootstrap.bundle.min.js` |

### 1.2 Struktur Folder Views

```
resources/views/
├── home.blade.php              # Dashboard setelah login
├── welcome.blade.php           # Halaman landing default Laravel
├── layouts/
│   └── app.blade.php           # Layout utama (semua halaman extends ini)
├── components/
│   └── product-card.blade.php  # Komponen kartu produk (reusable)
├── products/                   # Manajemen produk publik
│   ├── index.blade.php         # Grid produk + filter + pagination
│   └── show.blade.php          # Detail produk + form keranjang
├── cart/
│   └── index.blade.php         # Keranjang belanja (berbasis session)
├── checkout/
│   └── create.blade.php        # Konfirmasi checkout
├── orders/
│   └── show.blade.php          # Detail pesanan customer + stepper status
├── admin/
│   ├── products/               # Admin CRUD produk
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── orders/                 # Admin manajemen pesanan
│       ├── index.blade.php
│       └── show.blade.php
├── auth/                       # Autentikasi
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── verify.blade.php
│   └── passwords/
│       ├── confirm.blade.php
│       ├── email.blade.php
│       └── reset.blade.php
└── shop/                       # Versi demo/statis (placeholder + localStorage)
    ├── index.blade.php
    ├── products.blade.php
    ├── product.blade.php
    └── cart.blade.php
```

### 1.3 Pola Blade yang Umum Dipakai

| Directive | Fungsi |
|-----------|--------|
| `@extends('layouts.app')` | Menentukan layout induk |
| `@section('content')` / `@endsection` | Mengisi bagian konten layout |
| `@section('title', ...)` | Mengisi judul halaman |
| `@yield('content')`, `@yield('title')` | Menampilkan section di layout |
| `@push('styles')` / `@stack('styles')` | Menambahkan CSS inline |
| `@push('scripts')` / `@stack('scripts')` | Menambahkan JS inline |
| `@auth` / `@guest` / `@else` | Cek status login |
| `@error('field')` / `@enderror` | Menampilkan pesan validasi |
| `@forelse(...)` / `@empty` / `@endforelse` | Loop dengan kondisi kosong |
| `@php` / `@endphp` | Menjalankan kode PHP apa adanya |
| `@csrf` / `@method('PATCH')` | Token CSRF & spoofing method |

---

## 2. Layout Utama — `layouts/app.blade.php`

Layout ini menjadi kerangka semua halaman. Berisi **navbar**, **session flash message**, dan area **`@yield('content')`**.

### Contoh kode — Struktur dasar

```blade
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @stack('styles')  {{-- CSS tambahan per halaman --}}
</head>
<body>
    <main class="py-4">
        <div class="container">
            {{-- Menampilkan flash message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                </div>
            @endif
        </div>
        @yield('content')  {{-- Konten utama halaman --}}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')  {{-- JS tambahan per halaman --}}
</body>
</html>
```

### Contoh kode — Navbar dengan menu peran (role)

```blade
<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>

        <!-- Kiri: menampilkan opsi berdasarkan role -->
        <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">Produk</a>

        @auth
            @if(auth()->user()->role === 'admin')
                <a class="nav-link" href="{{ route('admin.products.index') }}">Admin Produk</a>
                <a class="nav-link" href="{{ route('admin.orders.index') }}">Admin Pesanan</a>
            @endif
        @endauth

        <!-- Kanan: link login/register atau profil -->
        @guest
            <a class="nav-link" href="{{ route('login') }}">Login</a>
            <a class="nav-link" href="{{ route('register') }}">Register</a>
        @else
            {{ Auth::user()->name }}  (menu dropdown, dengan form logout)
        @endguest
    </div>
</nav>
```

### Contoh kode — Menghitung jumlah item di keranjang (dari session)

```blade
@php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
@if($cartCount > 0)
    <span class="badge bg-danger ms-1">{{ $cartCount }}</span>
@endif
```

---

## 3. Halaman Utama

### 3.1 `home.blade.php` — Dashboard Setelah Login

Pola paling dasar penggunaan layout: `@extends` + `@section('content')`.

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                    @endif
                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### 3.2 `welcome.blade.php` — Halaman Landing Default

Halaman ini **tidak memakai layout** (HTML lengkap berdiri sendiri) dan menyediakan link masuk/daftar.

```blade
@if(Route::has('login'))
    <div class="top-right links">
        @auth
            <a href="{{ url('/home') }}">Home</a>
        @else
            <a href="{{ route('login') }}">Login</a>
            @if(Route::has('register'))
                <a href="{{ route('register') }}">Register</a>
            @endif
        @endauth
    </div>
@endif
```

### 3.3 `shop/index.blade.php` — Beranda Toko (versi Demo)

Menampilkan hero, kategori, produk unggulan, flash sale, dan newsletter. Memakai **component include** dan **`@push('scripts')`** untuk countdown timer.

#### Contoh — Include component dengan parameter

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

#### Contoh — Push script countdown

```blade
@push('scripts')
<script>
function startCountdown() {
    const countDownDate = new Date().getTime() + (24 * 60 * 60 * 1000);
    const timer = setInterval(function() {
        const distance = countDownDate - new Date().getTime();
        const hours = Math.floor((distance % (1000*60*60*24)) / (1000*60*60));
        const minutes = Math.floor((distance % (1000*60*60)) / (1000*60));
        const seconds = Math.floor((distance % (1000*60)) / 1000);
        document.getElementById("countdown").innerHTML =
            String(hours).padStart(2,'0') + ":" +
            String(minutes).padStart(2,'0') + ":" +
            String(seconds).padStart(2,'0');
        if (distance < 0) { clearInterval(timer); document.getElementById("countdown").innerHTML = "BERAKHIR"; }
    }, 1000);
}
document.addEventListener('DOMContentLoaded', startCountdown);
</script>
@endpush
```

> **Catatan:** Folder `shop/` adalah versi **demo/statis** — datanya `placeholder` (hardcoded), gambar dari `via.placeholder.com`, dan keranjang memakai `localStorage` di browser, **bukan** data dari database/session.

---

## 4. Autentikasi — folder `auth/`

Semua halaman auth mengikuti pola yang sama: `@extends('layouts.app')`, form dengan `@csrf`, validasi memakai `@error`.

### 4.1 `auth/login.blade.php` — Login

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### 4.2 `auth/register.blade.php` — Registrasi

Pola yang sama dengan login, ditambah field `name` dan **konfirmasi password** (`password_confirmation`).

```blade
<div class="row mb-3">
    <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>
    <div class="col-md-6">
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
    </div>
</div>
```

### 4.3 `auth/verify.blade.php` — Verifikasi Email

```blade
@if (session('resent'))
    <div class="alert alert-success" role="alert">
        {{ __('A fresh verification link has been sent to your email address.') }}
    </div>
@endif

<form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
    @csrf
    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
        {{ __('click here to request another') }}
    </button>.
</form>
```

### 4.4 `auth/passwords/` — Lupa / Reset Password

- **`email.blade.php`** — form memasukkan email untuk meminta link reset (`route('password.email')`), menampilkan `session('status')`.
- **`reset.blade.php`** — form reset dengan token tersembunyi (`route('password.update')`).
- **`confirm.blade.php`** — konfirmasi password sebelum lanjut (`route('password.confirm')`).

```blade
{{-- reset.blade.php --}}
<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <input id="email" type="email"
           class="form-control @error('email') is-invalid @enderror"
           name="email" value="{{ $email ?? old('email') }}" required autofocus>
    @error('email')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror

    <input id="password" type="password"
           class="form-control @error('password') is-invalid @enderror"
           name="password" required autocomplete="new-password">
    @error('password')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror

    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
    <button type="submit" class="btn btn-primary">{{ __('Reset Password') }}</button>
</form>
```

---

## 5. Produk — folder `products/` (dinamis dari database)

### 5.1 `products/index.blade.php` — Grid Produk + Filter + Pagination

Halaman produk publik dengan:
- **Sidebar filter** (pencarian `q` + kategori `category_id`) lewat method GET.
- **Grid kartu produk** dengan badge stok & harga.
- **Form tambah ke keranjang** (`cart.add`).
- **Pagination** Bootstrap 5.

#### Contoh — Header dengan info jumlah produk

```blade
@section('title', 'Daftar Produk - ' . config('app.name', 'Laravel'))

<p class="text-muted small mb-0">
    @if($products->total() > 0)
        Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }} produk
        @if(request('q') || request('category_id'))
            <span class="ms-1">untuk
                @if(request('q')) <span class="badge bg-primary">“{{ request('q') }}”</span> @endif
                @if(request('category_id')) <span class="badge bg-secondary">
                    {{ $categories->firstWhere('id', request('category_id'))->name ?? '' }}</span> @endif
            </span>
        @endif
    @else
        Belum ada produk yang cocok
    @endif
</p>
```

#### Contoh — Form filter (GET)

```blade
<form method="GET" action="{{ route('products.index') }}" id="filterForm">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama produk...">

    <select name="category_id" class="form-select">
        <option value="">Semua Kategori</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                    {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <button type="submit" class="btn btn-primary">Terapkan</button>
    @if(request('q') || request('category_id'))
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Hapus Filter</a>
    @endif
</form>
```

#### Contoh — Kartu produk + form keranjang

```blade
@foreach($products as $product)
<div class="card h-100 border-0 shadow-sm">
    <a href="{{ route('products.show', $product) }}">
        <img src="{{ !$product->image ? asset('noimage.png') : asset('storage/' . $product->image) }}"
             class="w-100 h-100 object-fit-cover" alt="{{ $product->name }}"
             onerror="this.src='{{ asset('noimage.png') }}'">
    </a>

    @if(($product->stock ?? 0) <= 0)
        <span class="badge bg-danger position-absolute m-2">Habis</span>
    @elseif(($product->stock ?? 0) < 5)
        <span class="badge bg-warning text-dark position-absolute m-2">Sisa {{ $product->stock }}</span>
    @endif

    <div class="card-body">
        <h3 class="h6 card-title">{{ $product->name }}</h3>
        <div class="fw-bold text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</div>

        @if(($product->stock ?? 0) > 0)
            <form method="POST" action="{{ route('cart.add', $product) }}">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-primary btn-sm">Keranjang</button>
            </form>
        @else
            <button class="btn btn-secondary btn-sm" disabled>Stok Habis</button>
        @endif
    </div>
</div>
@endforeach
```

#### Contoh — Pagination Bootstrap 5

```blade
{{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
```

### 5.2 `products/show.blade.php` — Detail Produk

Menampilkan gambar, kategori, deskripsi, harga, stok, dan **form pembelian dengan pemilih jumlah**.

```blade
@section('title', $product->name . ' - ' . config('app.name', 'Laravel'))

<form method="POST" action="{{ route('cart.add', $product) }}">
    @csrf
    <label for="quantity" class="form-label">Banyaknya</label>
    <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
           id="quantity" value="{{ old('quantity', 1) }}" min="1" max="{{ $product->stock ?? 100 }}"
           @if(($product->stock ?? 0) <= 0) disabled @endif>
    @error('quantity')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <div class="form-text">Stok tersedia: {{ $product->stock ?? 0 }}</div>

    <button type="submit" class="btn btn-primary w-100" @if(($product->stock ?? 0) <= 0) disabled @endif>
        <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
    </button>
</form>
```

---

## 6. Keranjang & Checkout

### 6.1 `cart/index.blade.php` — Keranjang Belanja (berbasis session)

Data keranjang diambil dari `$items`, `$total` (dikirim dari controller). Menampilkan kondisi kosong vs isi, tabel item, dan ringkasan.

#### Contoh — Kondisi keranjang kosong

```blade
@if(empty($items) || count($items) === 0)
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h4>Keranjang Kosong</h4>
            <p class="text-muted">Belum ada produk di keranjang Anda.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Lihat Produk</a>
        </div>
    </div>
@else
    ...
@endif
```

#### Contoh — Tabel item keranjang (update quantity & hapus)

```blade
@foreach($items as $id => $item)
<tr>
    <td>
        <div class="fw-semibold">{{ $item['name'] }}</div>
        <small class="text-muted">Rp {{ number_format($item['price'], 0, ',', '.') }} x {{ $item['quantity'] }}</small>
    </td>
    <td class="text-center">
        <form method="POST" action="{{ route('cart.update', $id) }}" class="d-flex gap-1">
            @csrf
            @method('PATCH')
            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm text-center">
            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fas fa-check"></i></button>
        </form>
    </td>
    <td class="text-end fw-bold">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
    <td class="text-center">
        <form method="POST" action="{{ route('cart.remove', $id) }}" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
        </form>
    </td>
</tr>
@endforeach
```

#### Contoh — Ringkasan & tombol checkout

```blade
<div class="card-body">
    <div class="d-flex justify-content-between mb-2">
        <span>Total Item</span>
        <span>{{ collect($items)->sum('quantity') }} item</span>
    </div>
    <div class="d-flex justify-content-between mb-3">
        <strong>Total</strong>
        <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
    </div>
    <a href="{{ route('checkout.create') }}" class="btn btn-success w-100 mb-2">Lanjut Checkout</a>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">Lanjut Belanja</a>
</div>
```

### 6.2 `checkout/create.blade.php` — Konfirmasi Checkout

Menampilkan ringkasan pesanan (`$cart`, `$total`) dan form submit untuk membuat pesanan.

```blade
<form method="POST" action="{{ route('checkout.store') }}">
    @csrf
    <p class="text-muted small">
        Dengan menekan tombol di bawah, pesanan akan dibuat dengan status
        <span class="badge bg-warning text-dark">pending</span>. Stok akan diverifikasi ulang di server.
    </p>
    <button type="submit" class="btn btn-success btn-lg w-100"><i class="fas fa-check"></i> Buat Pesanan</button>
</form>
```

---

## 7. Pesanan — `orders/show.blade.php` (Sisi Customer)

Halaman detail pesanan milik pelanggan. Memuat:
- **Badge status** (dari mapping warna).
- **Stepper** proses pesanan (pending → diproses → dikirim → selesai).
- **Tombol "Tandai Selesai"** saat status `dikirim` (dengan modal konfirmasi).
- **Tabel item pesanan**.

### Contoh — Mapping warna status

```blade
@php
    $badgeMap = ['pending'=>'warning','diproses'=>'info','dikirim'=>'primary','selesai'=>'success'];
    $badge = $badgeMap[$order->status] ?? 'secondary';
@endphp
<span class="badge bg-{{ $badge }} fs-6">{{ ucfirst($order->status) }}</span>
```

### Contoh — Stepper progres status

```blade
@php
    $steps=['pending','diproses','dikirim','selesai'];
    $idx=array_search($order->status,$steps);
@endphp
@foreach($steps as $i=>$step)
    @php $isDone=$i<$idx; $isActive=$i===$idx; @endphp
    <span class="badge rounded-pill px-3 py-2
        @if($isActive) bg-primary
        @elseif($isDone) bg-success
        @else bg-light text-muted border @endif">
        @if($isDone)<i class="fas fa-check me-1"></i>@endif {{ ucfirst($step) }}
    </span>
    @if($i < count($steps)-1)<i class="fas fa-chevron-right text-muted small mx-1"></i>@endif
@endforeach
```

### Contoh — Aksi customer: tandai "selesai"

```blade
@if(auth()->id() === $order->user_id)
    @if($order->status === 'dikirim')
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmSelesaiModal">
            <i class="fas fa-check me-1"></i> Tandai Selesai
        </button>
    @elseif($order->status === 'selesai')
        <div class="alert alert-success">Pesanan selesai — terima kasih!</div>
    @elseif(in_array($order->status, ['pending','diproses']))
        <div class="alert alert-info">Pesanan sedang diproses. Status akan diperbarui oleh admin menjadi Dikirim.</div>
    @endif
@endif
```

### Contoh — Modal konfirmasi + form PATCH status

```blade
<form id="formSelesai" method="POST" action="{{ route('orders.status', $order) }}">
    @csrf
    @method('PATCH')
    <input type="hidden" name="status" value="selesai">
    <button type="submit" class="btn btn-success">Ya, Tandai Selesai</button>
</form>
```

---

## 8. Admin — folder `admin/`

### 8.1 `admin/products/index.blade.php` — Daftar Produk (Admin)

Tabel dengan kolom: `#`, Nama (dengan thumbnail), Harga, Stok, Kategori, dan Aksi (lihat/edit/hapus).

```blade
@forelse($products as $product)
<tr>
    <td>{{ $product->id }}</td>
    <td>
        <div class="d-flex align-items-center gap-2">
            <img src="{{ !$product->image ? asset('noimage.png') : asset('storage/' . $product->image) }}"
                 alt="{{ $product->name }}" style="width: 40px; height: 40px; object-fit: cover;" class="rounded">
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
    <td>
        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></a>
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline"
              onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted py-4">Belum ada produk.</td></tr>
@endforelse
```

### 8.2 `admin/products/create.blade.php` — Tambah Produk

Form dengan `enctype="multipart/form-data"` untuk upload gambar. Memakai `old()` untuk mengingat input dan `@error` untuk validasi.

```blade
<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name') }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Harga <span class="text-danger">*</span></label>
        <input type="number" name="price" value="{{ old('price') }}"
               class="form-control @error('price') is-invalid @enderror" min="0" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Gambar</label>
        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Maks 2MB, format gambar.</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Kategori <span class="text-danger">*</span></label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">Pilih Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Stok</label>
        <input type="number" name="stock" value="{{ old('stock', 0) }}" class="form-control" min="0">
    </div>

    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Produk</button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
</form>
```

### 8.3 `admin/products/edit.blade.php` — Edit Produk

Mirip dengan create, ditambah:
- **@method('PUT')** untuk spoofing method HTTP.
- **Preview gambar lama**.
- `old('field', $product->field)` — nilai default dari data produk saat ini.

```blade
<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- Preview gambar lama --}}
    <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('noimage.png') }}"
         class="img-thumbnail" style="max-height: 180px; object-fit: cover;">

    <input type="text" name="name" value="{{ old('name', $product->name) }}"
           class="form-control @error('name') is-invalid @enderror" required>

    <select name="category_id" class="form-select" required>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <textarea name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="form-control" min="0">

    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
</form>
```

### 8.4 `admin/orders/index.blade.php` — Daftar Pesanan (Admin)

Tabel pesanan dengan **filter status & pencarian**, ditambah **aksi ubah status** sesuai alur admin (pending → diproses → dikirim).

```blade
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
    <td>
        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
        @if(!empty($nextOptions))
            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                @csrf
                @method('PATCH')
                <select name="status" class="form-select form-select-sm">
                    @foreach($nextOptions as $opt)
                        <option value="{{ $opt }}">{{ ucfirst($opt) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-check"></i></button>
            </form>
        @else
            @if($order->status==='selesai') Selesai @else Menunggu customer @endif
        @endif
    </td>
</tr>
```

#### Contoh — Filter status & pencarian (GET)

```blade
<form method="GET" action="{{ route('admin.orders.index') }}">
    <select name="status" class="form-select">
        <option value="">Semua Status</option>
        @foreach(['pending','diproses','dikirim','selesai'] as $st)
            <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
        @endforeach
    </select>
    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
           placeholder="Cari #ID / Nama / Email pelanggan...">
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Reset</a>
</form>
```

### 8.5 `admin/orders/show.blade.php` — Detail Pesanan (Admin)

Mirip dengan `orders/show` customer, tetapi menampilkan **Panel Admin** untuk mengubah status sesuai alur.

```blade
<div class="card border-warning shadow-sm mb-4">
    <div class="card-header bg-warning bg-opacity-10 fw-semibold">Panel Admin — Ubah Status</div>
    <div class="card-body">
        @php
            $adminFlow=['pending'=>['diproses'],'diproses'=>['dikirim'],'dikirim'=>[],'selesai'=>[]];
            $next=$adminFlow[$order->status]??[];
        @endphp

        @if($order->status==='selesai')
            <div class="alert alert-success mb-0">Pesanan sudah selesai. Tidak dapat diubah lagi.</div>
        @elseif(empty($next))
            <div class="alert alert-info mb-0">Menunggu customer tandai Selesai.</div>
        @else
            <p>Dari <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
               hanya bisa ke <span class="badge bg-primary">{{ ucfirst($next[0]) }}</span></p>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                @csrf
                @method('PATCH')
                <select name="status" class="form-select w-auto @error('status') is-invalid @enderror">
                    @foreach($next as $opt)
                        <option value="{{ $opt }}">{{ ucfirst($opt) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
            </form>
        @endif
    </div>
</div>
```

---

## 9. Komponen — `components/product-card.blade.php`

Komponen kartu produk yang **reusable** (dipakai ulang lewat `@include`). Mendukung parameter opsional dengan nilai default.

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
        <span class="badge {{ $badgeClass }} position-absolute" style="top: 10px; left: 10px;">{{ $badge }}</span>
    @endif

    <img src="{{ $image }}" class="card-img-top w-100 h-100" style="object-fit: cover;" alt="{{ $name }}">

    <div class="card-body d-flex flex-column">
        <h6 class="card-title mb-2">{{ $name }}</h6>

        @if($rating > 0)
        <div class="mb-2">
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
        @endif

        <div class="price-section mb-3">
            <div class="price">{{ $price }}</div>
            @if($originalPrice)
                <small class="text-muted text-decoration-line-through">{{ $originalPrice }}</small>
            @endif
        </div>

        <div class="mt-auto">
            <button class="btn btn-primary btn-sm w-100"
                    onclick="addToCart({{ $id }}, '{{ addslashes($name) }}', '{{ $price }}', '{{ $image }}')">
                <i class="fas fa-cart-plus"></i> Keranjang
            </button>
            <a href="{{ url('/product/' . $id) }}" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </div>
</div>
```

**Cara pakai:**
```blade
@include('components.product-card', [
    'id' => 1,
    'name' => 'Laptop Gaming',
    'price' => 'Rp 15.000.000',
    'rating' => 4.5,
    'reviews' => 128
])
```

---

## 10. Folder `shop/` — Catatan Versi Demo

Folder `shop/` berisi halaman **demo/statis**:

| File | Fungsi | Sifat |
|------|--------|-------|
| `index.blade.php` | Beranda toko (hero, kategori, produk unggulan, flash sale) | Statis |
| `products.blade.php` | Katalog produk dengan filter client-side (JS) | Statis, filter via JS & localStorage |
| `product.blade.php` | Detail produk dengan tabs, review, galeri | Statis, data hardcoded |
| `cart.blade.php` | Keranjang dengan pilihan ongkir & promo | Statis, menggunakan `localStorage` |

**Perbedaan kunci dengan view dinamis:**
- Data produk pada `shop/` bersifat **hardcoded** (placeholder), sedangkan `products/`, `cart/`, `orders/` mengambil data dari **database/session**.
- Filter & keranjang pada `shop/` dijalankan di **sisi client (JavaScript + localStorage)**, bukan dikirim ke server.
- Versi dinamis menggunakan `route()` + `@csrf`, sedangkan versi shop memakai URL langsung (`url('/product/...')`) dan javascript.

> Direkomendasikan memakai versi dinamis (`products/`, `cart/`, `orders/`) untuk produksi.

---

## 11. Lampiran — Daftar Lengkap File View

```
resources/views/
├── admin/
│   ├── orders/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   └── products/
│       ├── create.blade.php
│       ├── edit.blade.php
│       └── index.blade.php
├── auth/
│   ├── passwords/
│   │   ├── confirm.blade.php
│   │   ├── email.blade.php
│   │   └── reset.blade.php
│   ├── login.blade.php
│   ├── register.blade.php
│   └── verify.blade.php
├── cart/
│   └── index.blade.php
├── checkout/
│   └── create.blade.php
├── components/
│   └── product-card.blade.php
├── layouts/
│   └── app.blade.php
├── orders/
│   └── show.blade.php
├── products/
│   ├── index.blade.php
│   └── show.blade.php
├── shop/
│   ├── cart.blade.php
│   ├── index.blade.php
│   ├── product.blade.php
│   └── products.blade.php
├── home.blade.php
└── welcome.blade.php
```

---

*Dokumen ini dibuat otomatis sebagai rangkuman pembelajaran struktur views pada proyek BelajarLaravel.*
