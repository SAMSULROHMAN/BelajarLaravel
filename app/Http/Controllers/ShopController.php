<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Data sample produk untuk demo
     */
    private function getProducts()
    {
        return [
            [
                'id' => 1,
                'name' => 'Laptop Gaming ASUS ROG Strix G15',
                'price' => 15000000,
                'original_price' => 18000000,
                'category' => 'elektronik',
                'brand' => 'asus',
                'rating' => 4.5,
                'reviews' => 128,
                'stock' => 25,
                'image' => 'https://via.placeholder.com/600x400?text=Laptop+Gaming+ASUS+ROG',
                'images' => [
                    'https://via.placeholder.com/600x400?text=Laptop+Gaming+ASUS+ROG',
                    'https://via.placeholder.com/150x100?text=Image+1',
                    'https://via.placeholder.com/150x100?text=Image+2',
                    'https://via.placeholder.com/150x100?text=Image+3',
                    'https://via.placeholder.com/150x100?text=Image+4',
                ],
                'description' => 'ASUS ROG Strix G15 adalah laptop gaming yang dirancang khusus untuk para gamer dan content creator yang membutuhkan performa tinggi.',
                'specifications' => [
                    'Processor' => 'AMD Ryzen 7 6800H (8-core, 16-thread)',
                    'RAM' => '16GB DDR5-4800',
                    'Storage' => '512GB PCIe 4.0 NVMe SSD',
                    'Graphics' => 'NVIDIA GeForce RTX 3070 8GB GDDR6',
                    'Display' => '15.6" FHD (1920x1080) 144Hz IPS',
                    'OS' => 'Windows 11 Home'
                ],
                'badge' => 'Diskon 17%',
                'badge_class' => 'bg-danger'
            ],
            [
                'id' => 2,
                'name' => 'Smartphone Samsung Galaxy S23',
                'price' => 8500000,
                'category' => 'elektronik',
                'brand' => 'samsung',
                'rating' => 4.8,
                'reviews' => 95,
                'stock' => 50,
                'image' => 'https://via.placeholder.com/300x200?text=Samsung+S23',
                'description' => 'Samsung Galaxy S23 dengan kamera 50MP dan prosesor Snapdragon 8 Gen 2.',
                'badge' => 'Terlaris',
                'badge_class' => 'bg-success'
            ],
            [
                'id' => 3,
                'name' => 'Sepatu Nike Air Jordan',
                'price' => 2800000,
                'category' => 'fashion',
                'brand' => 'nike',
                'rating' => 4.6,
                'reviews' => 203,
                'stock' => 15,
                'image' => 'https://via.placeholder.com/300x200?text=Nike+Jordan',
                'description' => 'Sepatu basketball ikonik dengan teknologi Air cushioning.',
                'badge' => 'Terbaru',
                'badge_class' => 'bg-info'
            ],
            [
                'id' => 4,
                'name' => 'Kamera Canon EOS R6',
                'price' => 35000000,
                'category' => 'elektronik',
                'brand' => 'canon',
                'rating' => 4.9,
                'reviews' => 67,
                'stock' => 8,
                'image' => 'https://via.placeholder.com/300x200?text=Canon+R6',
                'description' => 'Kamera mirrorless full-frame dengan sensor 20MP dan video 4K.',
                'badge' => 'Premium',
                'badge_class' => 'bg-warning'
            ],
            [
                'id' => 5,
                'name' => 'Headphone Sony WH-1000XM4',
                'price' => 2500000,
                'original_price' => 5000000,
                'category' => 'elektronik',
                'brand' => 'sony',
                'rating' => 4.7,
                'reviews' => 156,
                'stock' => 30,
                'image' => 'https://via.placeholder.com/300x200?text=Sony+Headphone',
                'description' => 'Headphone wireless dengan Active Noise Cancelling.',
                'badge' => 'Flash Sale 50%',
                'badge_class' => 'bg-danger'
            ]
        ];
    }

    /**
     * Halaman beranda toko
     */
    public function index()
    {
        $featuredProducts = collect($this->getProducts())->take(4);
        $flashSaleProducts = collect($this->getProducts())->skip(4)->take(4);
        
        return view('shop.index', compact('featuredProducts', 'flashSaleProducts'));
    }

    /**
     * Halaman daftar produk dengan filter
     */
    public function products(Request $request)
    {
        $products = collect($this->getProducts());
        
        // Filter berdasarkan kategori
        if ($request->has('category') && $request->category) {
            $products = $products->where('category', $request->category);
        }
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search) {
            $products = $products->filter(function ($product) use ($request) {
                return stripos($product['name'], $request->search) !== false;
            });
        }
        
        // Filter berdasarkan harga
        if ($request->has('min_price') && $request->min_price) {
            $products = $products->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && $request->max_price) {
            $products = $products->where('price', '<=', $request->max_price);
        }
        
        // Filter berdasarkan rating
        if ($request->has('rating') && $request->rating) {
            $products = $products->where('rating', '>=', $request->rating);
        }
        
        // Filter berdasarkan brand
        if ($request->has('brands') && is_array($request->brands)) {
            $products = $products->whereIn('brand', $request->brands);
        }
        
        // Sorting
        $sortBy = $request->get('sort', 'default');
        switch ($sortBy) {
            case 'name-asc':
                $products = $products->sortBy('name');
                break;
            case 'name-desc':
                $products = $products->sortByDesc('name');
                break;
            case 'price-asc':
                $products = $products->sortBy('price');
                break;
            case 'price-desc':
                $products = $products->sortByDesc('price');
                break;
            case 'rating-desc':
                $products = $products->sortByDesc('rating');
                break;
            case 'newest':
                $products = $products->sortByDesc('id');
                break;
        }
        
        // Pagination simulation
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $total = $products->count();
        $products = $products->forPage($currentPage, $perPage);
        
        // Categories for filter
        $categories = [
            'elektronik' => 'Elektronik',
            'fashion' => 'Fashion', 
            'rumah-tangga' => 'Rumah Tangga',
            'olahraga' => 'Olahraga',
            'buku' => 'Buku & Alat Tulis'
        ];
        
        // Brands for filter
        $brands = [
            'asus' => 'ASUS',
            'samsung' => 'Samsung',
            'apple' => 'Apple',
            'nike' => 'Nike',
            'sony' => 'Sony',
            'canon' => 'Canon'
        ];
        
        return view('shop.products', compact('products', 'categories', 'brands', 'total'));
    }

    /**
     * Detail produk
     */
    public function product($id)
    {
        $product = collect($this->getProducts())->where('id', $id)->first();
        
        if (!$product) {
            abort(404, 'Produk tidak ditemukan');
        }
        
        // Related products (same category, exclude current)
        $relatedProducts = collect($this->getProducts())
            ->where('category', $product['category'])
            ->where('id', '!=', $id)
            ->take(4);
        
        return view('shop.product', compact('product', 'relatedProducts'));
    }

    /**
     * Halaman keranjang belanja
     */
    public function cart()
    {
        return view('shop.cart');
    }

    /**
     * API untuk mendapatkan data produk (untuk AJAX)
     */
    public function getProductsApi(Request $request)
    {
        $products = collect($this->getProducts());
        
        // Apply filters similar to products() method
        if ($request->has('category') && $request->category) {
            $products = $products->where('category', $request->category);
        }
        
        if ($request->has('search') && $request->search) {
            $products = $products->filter(function ($product) use ($request) {
                return stripos($product['name'], $request->search) !== false;
            });
        }
        
        return response()->json([
            'products' => $products->values(),
            'total' => $products->count()
        ]);
    }

    /**
     * Get categories with count
     */
    public function getCategories()
    {
        $products = collect($this->getProducts());
        $categories = $products->groupBy('category')->map(function ($items, $key) {
            return [
                'name' => $key,
                'count' => $items->count(),
                'display_name' => ucwords(str_replace('-', ' ', $key))
            ];
        });
        
        return response()->json($categories);
    }

    /**
     * Get product by ID (API)
     */
    public function getProduct($id)
    {
        $product = collect($this->getProducts())->where('id', $id)->first();
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        
        return response()->json($product);
    }
}