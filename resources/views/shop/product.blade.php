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
                            <button class="btn btn-primary btn-lg w-100" 
                                    onclick="addToCartWithQuantity()">
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
                                            <span class="me-2">5★</span>
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: 65%"></div>
                                            </div>
                                            <span class="small text-muted">83</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2">4★</span>
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: 20%"></div>
                                            </div>
                                            <span class="small text-muted">26</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2">3★</span>
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: 10%"></div>
                                            </div>
                                            <span class="small text-muted">13</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2">2★</span>
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: 3%"></div>
                                            </div>
                                            <span class="small text-muted">4</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">1★</span>
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
                                                <button class="btn btn-sm btn-outline-primary me-2">👍 Berguna (12)</button>
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
                                                <button class="btn btn-sm btn-outline-primary me-2">👍 Berguna (8)</button>
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