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