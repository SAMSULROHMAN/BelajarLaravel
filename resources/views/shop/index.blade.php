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