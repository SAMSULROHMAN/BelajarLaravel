# Rangkuman BelajarLaravel — Views, Controllers, Models & Routes

> **Update 30 Aug 2026 — Tambahan:** Flow Update Produk Admin (`admin/products/edit` + `update` hapus gambar lama, `show` reuse)



> **Tanggal:** 30 Aug 2026  
> **Stack:** Laravel 13.17 (PHP 8.3), Bootstrap 5.3.8, FontAwesome 6.5.2, laravel/ui 4.6  
> **Scope Dokumen:** P0 Fondasi Layout → Rebuild `/products` Responsive → Fitur Admin Order (lihat & ubah status by role)

---

## 1. Ringkasan Eksekusi

| Tahap | Fokus | Status |
|---|---|---|
| **P0 Fondasi** | Perbaiki `layouts/app`, bug `ProductController::show`, rebuild 5 view rusak (`cart`, `checkout`, `orders`, `admin/products`) | ✅ Selesai |
| **Rebuild `/products`** | Responsive Bootstrap 5 `products/index` + fix `scopeSearch` | ✅ Selesai |
| **Admin Order Flow** | `pending → diproses → dikirim (admin)` → `selesai (customer modal)` + filter `status` + search `q` | ✅ Selesai |
| **Dokumentasi** | File ini (`docs/RANGKUMAN.md` — opsi B) | ✅ |

Total route: **36 routes** (`php artisan route:list`)

---

## 2. Views (`resources/views`)

### 2.1 Baru Dibuat

| File | Deskripsi | Baris |
|---|---|---|
| `admin/orders/index.blade.php` | Daftar semua pesanan (admin). Filter `status` + `q` (`#ID / Nama / Email`), tabel `ID / Pelanggan / Tanggal / Total / Status badge / Aksi`, inline ubah status (hanya `next` admin), empty state, pagination `bootstrap-5` | ~122 |
| `admin/orders/show.blade.php` | Detail pesanan admin. Breadcrumb, info pelanggan (`user->name/email`), stepper `pending→diproses→dikirim→selesai`, panel `border-warning` form `PATCH admin.orders.status` (hanya `next`), tabel items | ~92 |

### 2.2 Rewrite Total

| File | Sebelum | Sesudah | Poin Utama |
|---|---|---|---|
| `products/index.blade.php` | 45 baris, form polos tanpa class, `width:18rem` tidak responsive, tanpa empty/pagination `bootstrap-5` | ~155 baris | `container py-3`, header `firstItem-lastItem/total` + badge `q/category`, filter `collapse d-lg-block sticky` (`q` + `category_id` retain), grid `row-cols-2 row-cols-md-3 row-cols-xl-4`, `ratio-4x3 object-fit-cover`, `product-hover translateY(-4px)`, clamp 2 baris, `add to cart POST` + `Lihat Detail`, empty card `fa-box-open`, `@push('styles')` |
| `products/show.blade.php` | `foreach($products)` bug (tampilkan semua), `id="order"`, tanpa stok | 73 baris | Single `$product` (`Product $product` binding), breadcrumb, badge stok (`success/danger`), form `POST cart.add` dengan `@error quantity`, `max=stock`, disabled jika habis |
| `cart/index.blade.php` | Bare `<div><tr>` tanpa layout/table | ~80 baris | `@extends`, breadcrumb, `table-hover` (`Produk/Qty/Subtotal/Aksi`), `PATCH cart.update` + `DELETE cart.remove`, ringkasan `collect($items)->sum quantity` + total, empty `fa-shopping-cart` |
| `checkout/create.blade.php` | Bare div, loop `cart` | ~55 baris | `@extends`, breadcrumb `Keranjang→Checkout`, `table` ringkasan + `tfoot total`, form `POST checkout.store` `@csrf`, info `pending` |
| `orders/show.blade.php` | Bare div 13 baris | ~95 baris | `@extends`, breadcrumb, badge `pending=warning/diproses=info/dikirim=primary/selesai=success`, stepper read-only, card `dikirim→selesai` **modal konfirmasi** `Apakah barang sudah diterima?` (`data-bs-target #confirmSelesaiModal` + form `PATCH orders.status selesai`), `updated_at` saat selesai, tabel items |
| `admin/products/index.blade.php` | Bare div, `tr` tanpa table | 71 baris | `@extends`, `table-hover` `ID/Nama/Harga/Stok/Kategori/Aksi`, thumbnail `storage`, badge stok, `links pagination::bootstrap-5` |
| `admin/products/create.blade.php` | Bare div, input tanpa label/error | ~90 baris | `@extends`, `label + old() + @error`, `name/price/image/category_id` + baru `description/stock`, `enctype`, `form-text` |

### 2.3 Fondasi Diperbaiki

| File | Perubahan |
|---|---|
| `layouts/app.blade.php` | `5.0.2 → 5.3.8` Bootstrap, tambah FontAwesome 6.5.2, `@yield('title')` + `@stack('styles'/'scripts')`, nav kiri `Produk / Keranjang (badge count session) / Admin Produk / Admin Pesanan (role===admin)`, alert `success/error/status` dismissible, `container` flash di `main.py-4` |
| `components/product-card.blade.php` | Tidak diubah (73 baris) — masih pakai `addslashes` + `via.placeholder.com` (deprecated) — catatan untuk refactor pakai `Js::from()` + `placehold.co` |
| `shop/index.blade.php`, `shop/products.blade.php` (608 baris), `shop/product.blade.php`, `shop/cart.blade.php`, `welcome.blade.php` | Orphan (route `ShopController` di `routes/web.php:16` masih comment) — UI canggih tapi tidak dipakai, `localStorage` cart vs session cart dual system — keputusan hapus/aktifkan di luar scope P0 |

---

## 3. Controllers (`app/Http/Controllers`)

### 3.1 `ProductController.php` (`App\Http\Controllers`)

```php
// Sebelum
public function show(){ $products = Product::with('category')->get(); return view('products.show', compact('products')); }
// Sesudah (fix P0)
public function show(Product $product){ $product->load('category'); return view('products.show', compact('product')); }

public function index(Request $r){
  $products = Product::with('category')->search($r->q)->category($r->category_id)->latest()->paginate(12)->withQueryString();
  $categories = Category::select('id','name')->get();
  return view('products.index', compact('products','categories'));
}
```

### 3.2 `Admin/ProductController.php` (`App\Http\Controllers\Admin`)

```php
// Validasi store (P0)
$validated = $request->validate([
  'name'=>'required|string|max:255',
  'price'=>'required|integer|min:0',
  'image'=>'nullable|image|max:2048',
  'category_id'=>'required|exists:categories,id',
  'description'=>'nullable|string', // baru
  'stock'=>'nullable|integer|min:0', // baru
]);
```

`index()` → `Product::latest()->paginate(15)`, `destroy(Product $product)` tetap.

### 3.3 `Admin/OrderController.php` — REWRITE TOTAL (22 → 61 baris)

```php
private const ADMIN_FLOW = ['pending'=>['diproses'],'diproses'=>['dikirim'],'dikirim'=>[],'selesai'=>[]];
public function index(Request $r){ // filter status + q
  $q=Order::with(['user','items.product'])->latest();
  if($r->filled('status') && array_key_exists($r->status,self::ADMIN_FLOW)) $q->where('status',$r->status);
  if($r->filled('q')){ $kw=trim($r->q); $q->where(fn($qq)=> $qq->orWhere('id',$kw)->orWhereHas('user', fn($u)=>$u->where('name','like',"%$kw%")->orWhere('email','like',"%$kw%"))); }
  return view('admin.orders.index',['orders'=>$q->paginate(15)->withQueryString()]);
}
public function show(Order $order){ return view('admin.orders.show',['order'=>$order->load(['user','items.product'])]); }
public function updateStatus(Request $r, Order $order){
  $r->validate(['status'=>'required|in:pending,diproses,dikirim,selesai']);
  if($r->status==='selesai') return back()->with('error','Selesai hanya customer');
  $allowed=self::ADMIN_FLOW[$order->status]??[];
  if(!in_array($r->status,$allowed,true)) return back()->with('error',"Dari {$order->status} hanya bisa ke: ".implode(',',$allowed));
  $order->update(['status'=>$r->status]); return back()->with('success','...'.ucfirst($r->status));
}
```

### 3.4 `OrderController.php` — TAMBAH 1 METHOD

```php
public function show(Order $order){
  abort_unless($order->user_id===auth()->id() || auth()->user()?->role==='admin',403);
  return view('orders.show',['order'=>$order->load(['user','items.product'])]);
}
public function updateStatus(Request $r, Order $order){ // customer
  abort_unless($order->user_id===auth()->id(),403);
  $r->validate(['status'=>'required|in:selesai']);
  if($order->status!=='dikirim') return back()->with('error','Hanya Dikirim bisa Selesai');
  $order->update(['status'=>'selesai']); return back()->with('success','Terima kasih, selesai');
}
```

### 3.5 `CheckoutController.php` — FIX P0 (65 → 70 baris)

```php
$order = DB::transaction(fn()=> Order::create([...])->items()->create(...)->decrement('stock',...));
session()->forget('cart'); // baru
return redirect()->route('orders.show',$order)->with('success','Pesanan berhasil dibuat.'); // baru
```

### 3.6 `CartController.php` — Tetap

`index()` → `session('cart')` + `total sum`, `add(Request,Product)` merge `quantity`, `update`/`remove` — dipakai `cart/index` baru.

---

## 4. Models (`app/Models`)

| Model | Field / Relasi | Perubahan |
|---|---|---|
| `Product.php` | `fillable category_id,name,description,price,stock,image`, `belongsTo Category`, `scopeSearch`, `scopeCategory` | **Fix bug** `scopeSearch`: `'%{$keyword}%'` → `'%' . $keyword . '%'` (interpolasi single-quote sebelumnya mati) |
| `Category.php` | `hasMany Product` | Tidak diubah |
| `Order.php` | `fillable user_id,total,status`, `hasMany items`, `belongsTo user` | Tidak diubah — dipakai eager `with(['user','items.product'])` |
| `OrderItem.php` | `fillable order_id,product_id,quantity,price`, `belongsTo order/product` | Tidak diubah |
| `User.php` | `#[Fillable(['name','email','password'])]`, `casts email_verified_at,password hashed` | Tidak diubah — `role` (`customer` default, `admin`) ada via migration tapi belum di Fillable (tidak impact read; perlu tambah jika mass-assign admin) |

**Migrations terkait:**
- `2026_08_25_233625_create_products_table.php`: `category_id FK`, `name`, `description nullable`, `price unsignedInt`, `stock default 0`, `image nullable`
- `2026_08_25_233630_create_orders_table.php`: `orders id,user_id FK,total,status default pending`, `order_items id,order_id FK cascade,product_id FK,quantity,price`
- `2026_08_27_142102_add_role_to_users_table.php`: `role string default customer after email`

---

## 5. Routes (`routes/web.php`)

```php
Route::get('/', fn()=>view('welcome'));
Auth::routes();
Route::get('/products', [ProductController::class,'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class,'show'])->name('products.show');
Route::get('/home', [HomeController::class,'index'])->name('home');

Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function(){
  Route::resource('products', AdminProductController::class);
  Route::get('orders', [AdminOrderController::class,'index'])->name('orders.index'); // P0 sudah ada, kini ada method
  Route::get('orders/{order}', [AdminOrderController::class,'show'])->name('orders.show'); // BARU (7.3)
  Route::patch('orders/{order}/status', [AdminOrderController::class,'updateStatus'])->name('orders.status');
});
Route::middleware('auth')->group(function(){
  Route::get('/cart', [CartController::class,'index'])->name('cart.index');
  Route::post('/cart/{product}', [CartController::class,'add'])->name('cart.add');
  Route::patch('/cart/{product}', [CartController::class,'update'])->name('cart.update');
  Route::delete('/cart/{product}', [CartController::class,'remove'])->name('cart.remove');
  Route::get('/checkout', [CheckoutController::class,'create'])->name('checkout.create');
  Route::post('/checkout', [CheckoutController::class,'store'])->name('checkout.store');
  Route::get('/orders/{order}', [OrderController::class,'show'])->name('orders.show');
  Route::patch('/orders/{order}/status', [OrderController::class,'updateStatus'])->name('orders.status'); // BARU (flow customer)
});
```

**Route `admin` middleware:** `bootstrap/app.php:15` → `$middleware->alias(['admin'=>AdminMiddleware::class])`, cek `role!=='admin' abort 403` (`AdminMiddleware.php:18`).

---

## 6. Flow Status Order — Diagram

```
[Customer] POST /checkout
      │
      ▼
   pending ──(admin: pending→diproses)──► diproses ──(admin: diproses→dikirim)──► dikirim
      │                                      │                                     │
      │ admin tidak bisa                     │ admin tidak bisa                    │──(customer modal: dikirim→selesai)──► selesai (final)
      │ langsung selesai                     │ lompat ke selesai                   │
      ▼                                      ▼                                     ▼
   ditolak "hanya customer"          ditolak "hanya ke dikirim"           admin lihat "Menunggu customer"
```

- **Admin UI:** `admin/orders/index` inline select hanya `next`; `admin/orders/show` panel `border-warning` + stepper `pending→diproses→dikirim→selesai` (selesai grey)
- **Customer UI:** `orders/show` stepper read-only + card `Dikirim` → tombol `Tandai Selesai` → **modal** `Apakah barang sudah diterima?` → `PATCH orders.status selesai`

---

## 7. Verifikasi Build

```bash
php artisan view:clear && php artisan view:cache
# → Blade templates cached successfully.

php -l app/Http/Controllers/Admin/OrderController.php # No syntax errors
php -l app/Http/Controllers/OrderController.php       # No syntax errors
php -l app/Models/Product.php                         # No syntax errors
php -l routes/web.php                                 # No syntax errors

php artisan route:list --path=orders
# GET  admin/orders              admin.orders.index   Admin\OrderController@index
# GET  admin/orders/{order}      admin.orders.show    Admin\OrderController@show
# PATCH admin/orders/{order}/status admin.orders.status Admin\OrderController@updateStatus
# GET  orders/{order}            orders.show          OrderController@show
# PATCH orders/{order}/status    orders.status        OrderController@updateStatus
```

**Test manual disarankan:**
- Login `admin@example.com (role=admin)` → `/admin/orders` filter `pending` + search `#ID`, coba `pending→diproses` (sukses), `pending→selesai` (error), `dikirim→selesai` (error “hanya customer”)
- Login `customer@example.com` → buat checkout → buka `orders/{id}` status `pending` (info “sedang diproses”), tunggu admin kirim → tombol `Tandai Selesai` muncul → klik → modal konfirmasi → `PATCH selesai` (sukses)

---

## 8. File Tree Perubahan (Ringkas)

```
app/
  Http/Controllers/
    ProductController.php                # fix show(Product $product)
    Admin/ProductController.php          # +description/stock validation
    Admin/OrderController.php            # REWRITE: index, show, ADMIN_FLOW
    OrderController.php                  # +updateStatus customer
    CheckoutController.php               # +forget cart + redirect
  Models/
    Product.php                          # fix scopeSearch
    Order.php / OrderItem.php / User.php # (read)
resources/views/
  layouts/app.blade.php                  # 5.3.8, FontAwesome, @stack, nav Admin Pesanan
  products/index.blade.php               # REWRITE responsive
  products/show.blade.php                # REWRITE single product
  cart/index.blade.php                   # REWRITE table
  checkout/create.blade.php              # REWRITE table + form
  orders/show.blade.php                  # REWRITE + stepper + modal Selesai
  admin/products/index.blade.php         # REWRITE table
  admin/products/create.blade.php        # REWRITE form
  admin/orders/index.blade.php           # BARU
  admin/orders/show.blade.php            # BARU
  components/product-card.blade.php      # (orphan, catatan)
  shop/* , welcome.blade.php             # (orphan)
routes/web.php                           # +2 routes (7.3)
database/migrations/*_create_orders* / *_add_role* # (read)
```

---

## 9. Catatan & Next Step

- `components/product-card.blade.php:61` masih `addslashes` + `via.placeholder.com` (deprecated) → ganti `placehold.co` + `Js::from()` jika diaktifkan
- `shop/*` 4 file (1500+ baris) route masih comment (`routes/web.php:16`) — pilih hapus atau aktifkan `ShopController` di iterasi berikutnya
- `User` Fillable belum include `role` — tambah `#[Fillable(['name','email','password','role'])]` jika buat admin via seeder
- `orders.index` untuk customer (daftar pesanan miliknya) belum ada — bisa tambah `GET /orders` list milik `auth()->id()` tanpa ubah flow

---

---

## 10. Update 30 Aug 2026 — Flow Update Produk Admin (Ganti/Keep Gambar)

**Keputusan:** Hapus gambar lama = ya (auto delete saat upload baru), hapus manual = tidak (ganti/keep), show admin reuse `products/show`.

**Controller `Admin/ProductController.php:61-92` (final):**
```php
use Illuminate\Support\Facades\Storage;

public function show(Product $product){ return redirect()->route('products.show',$product); }
public function edit(Product $product){
  $categories = Category::select('id','name')->get();
  return view('admin.products.edit', compact('product','categories'));
}
public function update(Request $r, Product $product){
  $validated = $r->validate([
    'name'=>'required|string|max:255','price'=>'required|integer|min:0',
    'image'=>'nullable|image|max:2048','category_id'=>'required|exists:categories,id',
    'description'=>'nullable|string','stock'=>'nullable|integer|min:0'
  ]);
  if($r->hasFile('image')){
    if($product->image) Storage::disk('public')->delete($product->image);
    $validated['image']=$r->file('image')->store('products','public');
  } else unset($validated['image']);
  $product->update($validated);
  return redirect()->route('admin.products.index')->with('success','Produk diperbarui.');
}
```

**View `admin/products/edit.blade.php` (BARU, ~95 baris):**
`@extends('layouts.app')` → breadcrumb `Produk Admin > Edit #id`, preview `asset('storage/'.$product->image)` `max-height 180px`, form `POST admin.products.update` `+ @method('PUT') enctype multipart`, `value="{{ old('field',$product->field) }}"` + `@selected`, `@error`, `Kosongkan jika tidak ganti`.

**Route:** Sudah ada `GET admin/products/{product}/edit` `admin.products.edit` + `PUT admin/products/{product}` `admin.products.update` (`Route::resource`) — `admin` middleware.

**Verifikasi:** `view:cache OK`, `route:list --path=admin/products` 7 routes, `php -l` OK — admin klik Edit di `admin/products/index:47` → form prefilled → upload baru auto hapus lama.

---

*Dokumen auto-generated dari sesi build plan→build. Untuk sinkron ulang, jalankan `php artisan view:clear && php artisan route:list`.*
