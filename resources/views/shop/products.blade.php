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
            tags.push(`Harga: ${minPrice || '0'} - ${maxPrice || '∞'}`);
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