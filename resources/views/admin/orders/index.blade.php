@extends('layouts.app')

@section('title', 'Admin - Daftar Pesanan')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1"><i class="fas fa-receipt me-2 text-primary"></i>Kelola Pesanan</h2>
            <p class="text-muted small mb-0">Total {{ $orders->total() }} pesanan</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter-status" class="form-label small fw-semibold">Status</label>
                    <select id="filter-status" name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(['pending','diproses','dikirim','selesai'] as $st)
                            <option value="{{ $st }}" @selected(request('status')===$st)>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="filter-q" class="form-label small fw-semibold">Pencarian Order</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="filter-q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari #ID / Nama / Email pelanggan...">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-1"></i> Filter</button>
                    @if(request('status') || request('q'))
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:80px;">#ID</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                        <th class="text-center" style="width:220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
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
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                @if(!empty($nextOptions))
                                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="d-flex gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm" style="width:130px;">
                                            @foreach($nextOptions as $opt)
                                                <option value="{{ $opt }}">{{ ucfirst($opt) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary" title="Ubah Status"><i class="fas fa-check"></i></button>
                                    </form>
                                @else
                                    <span class="text-muted small">
                                        @if($order->status==='selesai') Selesai @else Menunggu customer @endif
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                            Tidak ada pesanan ditemukan.
                            @if(request('status') || request('q'))
                                <a href="{{ route('admin.orders.index') }}" class="ms-1">Reset filter</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="card-footer bg-white d-flex justify-content-center">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
