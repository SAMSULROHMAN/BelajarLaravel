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
        <div class="card-header bg-warning bg-opacity-10 fw-semibold"><i class="fas fa-shield-alt me-1"></i> Panel Admin — Ubah Status</div>
        <div class="card-body">
            @php $adminFlow=['pending'=>['diproses'],'diproses'=>['dikirim'],'dikirim'=>[],'selesai'=>[]]; $next=$adminFlow[$order->status]??[]; @endphp
            @if($order->status==='selesai')
                <div class="alert alert-success mb-0"><i class="fas fa-check-circle me-1"></i> Pesanan sudah selesai (oleh customer). Tidak dapat diubah lagi.</div>
            @elseif(empty($next))
                <div class="alert alert-info mb-0"><i class="fas fa-info-circle me-1"></i> Pesanan status <strong>{{ ucfirst($order->status) }}</strong> — menunggu customer tandai <strong>Selesai</strong> setelah barang diterima.</div>
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
