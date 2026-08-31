@extends('layouts.app')

@section('title', 'Pesanan #' . $order->id)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Produk</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pesanan #{{ $order->id }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Pesanan #{{ $order->id }}</h2>
        @php
            $badgeMap = ['pending'=>'warning','diproses'=>'info','dikirim'=>'primary','selesai'=>'success'];
            $badge = $badgeMap[$order->status] ?? 'secondary';
        @endphp
        <span class="badge bg-{{ $badge }} fs-6">{{ ucfirst($order->status) }}</span>
    </div>

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row text-muted small">
                <div class="col-md-6">ID Pesanan: <strong class="text-dark">#{{ $order->id }}</strong></div>
                <div class="col-md-6 text-md-end">Tanggal: {{ $order->created_at->format('d M Y H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- Stepper customer --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            @php $steps=['pending','diproses','dikirim','selesai']; $idx=array_search($order->status,$steps); @endphp
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                @foreach($steps as $i=>$step)
                    @php $isDone=$i<$idx; $isActive=$i===$idx; @endphp
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge rounded-pill px-3 py-2 @if($isActive) bg-primary @elseif($isDone) bg-success @else bg-light text-muted border @endif">
                            @if($isDone)<i class="fas fa-check me-1"></i>@endif {{ ucfirst($step) }}
                        </span>
                        @if($i < count($steps)-1)<i class="fas fa-chevron-right text-muted small mx-1"></i>@endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Customer action: dikirim -> selesai --}}
    @if(auth()->id() === $order->user_id)
        @if($order->status === 'dikirim')
            <div class="card border-success shadow-sm mb-4">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div>
                        <div class="fw-semibold"><i class="fas fa-box-open me-1 text-success"></i> Pesanan sudah diterima?</div>
                        <div class="small text-muted">Klik Selesai jika barang telah sampai di tujuan.</div>
                    </div>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmSelesaiModal">
                        <i class="fas fa-check me-1"></i> Tandai Selesai
                    </button>
                </div>
            </div>
        @elseif($order->status === 'selesai')
            <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> Pesanan selesai pada {{ $order->updated_at->format('d M Y H:i') }} — terima kasih!</div>
        @elseif(in_array($order->status, ['pending','diproses']))
            <div class="alert alert-info small mb-4"><i class="fas fa-info-circle me-1"></i> Pesanan sedang diproses. Status akan diperbarui oleh admin menjadi Dikirim.</div>
        @endif
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Item Pesanan</div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
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
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end">Rp {{ number_format($order->total, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali Belanja</a>
        @if(auth()->user()?->role==='admin')
            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-primary">Kelola sebagai Admin</a>
        @endif
    </div>
</div>

{{-- Modal Konfirmasi Selesai --}}
@if(auth()->id() === $order->user_id && $order->status==='dikirim')
<div class="modal fade" id="confirmSelesaiModal" tabindex="-1" aria-labelledby="confirmSelesaiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmSelesaiLabel">Konfirmasi Pesanan Selesai</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Apakah barang pesanan <strong>#{{ $order->id }}</strong> sudah diterima dengan baik?
        <div class="small text-muted mt-2">Tindakan ini akan mengubah status menjadi <span class="badge bg-success">Selesai</span> dan tidak dapat dibatalkan.</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form id="formSelesai" method="POST" action="{{ route('orders.status', $order) }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="selesai">
            <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i> Ya, Tandai Selesai</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif
@endsection
