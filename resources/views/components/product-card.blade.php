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