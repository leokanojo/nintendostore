@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">
                📦 Persediaan Barang
            </h2>
            <p class="text-muted mb-0">
                Kelola transaksi barang masuk, barang keluar, dan pantau pergerakan stok persediaan.
            </p>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Validation Error --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Main Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Data Persediaan Barang</h4>
                    <small class="text-muted">
                        Tabel transaksi keluar masuk barang Toko Bangunan Jwp.
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm btn-barang-masuk">
                        Barang Masuk
                    </button>
                    <button type="button" class="btn btn-danger btn-sm btn-barang-keluar">
                        Barang Keluar
                    </button>
                </div>
            </div>

            {{-- Input Pencarian Lokal --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="searchInventory" class="form-control" placeholder="Cari kode, nama barang, atau kategori... (Tekan Enter)">
                </div>
            </div>

            {{-- Tabel Data --}}
            <div class="table-responsive">
                <table id="datatable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Stok Saat Ini</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            @php
                                $stockAfter = (int) $transaction->stock_after;
                                if ($stockAfter > 0) {
                                    $statusLabel = 'Tersedia';
                                    $statusClass = 'bg-success';
                                } else {
                                    $statusLabel = 'Tidak Tersedia';
                                    $statusClass = 'bg-danger';
                                }
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $transaction->transaction_date ? $transaction->transaction_date->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $transaction->product->code ?? '-' }}</span>
                                </td>
                                <td>
                                    <strong>{{ $transaction->product->name ?? '-' }}</strong>
                                    @if ($transaction->note)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($transaction->note, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $transaction->product->category->name ?? '-' }}</td>
                                <td>
                                    @if ($transaction->type === 'masuk')
                                        <span class="text-success fw-bold">
                                            {{ number_format($transaction->quantity) }}
                                        </span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($transaction->type === 'keluar')
                                        <span class="text-danger fw-bold">
                                            {{ number_format($transaction->quantity) }}
                                        </span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ number_format($stockAfter) }}</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Belum ada data transaksi persediaan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- BAGIAN PAGINASI CUSTOM (Tombol di Tengah, Keterangan di Bawahnya & di Tengah) --}}
@if($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $transactions->total() > 0)
    <div class="d-flex flex-column align-items-center justify-content-center gap-2 px-4 py-3 bg-white border-top">
        
        {{-- Row 1: Tombol Navigasi Paginasi (Paling Atas & Ditengah) --}}
        <div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm m-0 justify-content-center">
                    {{-- Tombol Previous --}}
                    @if ($transactions->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $transactions->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    {{-- Nomor Halaman --}}
                    @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $transactions->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    {{-- Tombol Next --}}
                    @if ($transactions->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $transactions->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>

        {{-- Row 2: Keterangan Data entries (Di Bawah & Ditengah) --}}
        <div class="text-muted small text-center w-100 mt-1">
            Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} entries
        </div>

    </div>
@else
    {{-- Jika data kosong --}}
    <div class="px-4 py-3 bg-white border-top text-muted small text-center">
        Showing 0 to 0 of 0 entries
    </div>
@endif
        </div>
    </div>
</div>

{{-- Modal Barang Masuk --}}
<div class="modal fade" id="modalBarangMasuk" tabindex="-1" aria-labelledby="modalBarangMasukLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('inventory.transaction.store') }}" method="POST" id="formBarangMasuk">
                @csrf
                <input type="hidden" name="type" value="masuk">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBarangMasukLabel">Form Barang Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" id="tanggal_masuk" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barang <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_masuk" class="form-select" required>
                                <option value="">Pilih Barang</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                        {{ $product->code }} - {{ $product->name }} | Stok: {{ $product->stock }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah Masuk <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity_masuk" class="form-control" min="1" placeholder="Contoh: 120" required>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="w-100 mb-1">
                                <small class="text-muted d-block" id="stok_masuk_info">Pilih barang untuk melihat stok saat ini.</small>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="note" class="form-control" rows="4" placeholder="Keterangan barang masuk"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Barang Masuk</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Barang Keluar --}}
<div class="modal fade" id="modalBarangKeluar" tabindex="-1" aria-labelledby="modalBarangKeluarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('inventory.transaction.store') }}" method="POST" id="formBarangKeluar">
                @csrf
                <input type="hidden" name="type" value="keluar">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBarangKeluarLabel">Form Barang Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" id="tanggal_keluar" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Barang <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_keluar" class="form-select" required>
                                <option value="">Pilih Barang</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-stock="{{ $product->stock }}" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                        {{ $product->code }} - {{ $product->name }} | Stok: {{ $product->stock }} {{ $product->stock <= 0 ? '| Tidak Tersedia' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jumlah Keluar <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity_keluar" class="form-control" min="1" placeholder="Contoh: 35" required>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="w-100 mb-1">
                                <small class="text-muted d-block" id="stok_keluar_info">Pilih barang untuk melihat batas stok keluar.</small>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="note" class="form-control" rows="4" placeholder="Keterangan barang keluar"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan Barang Keluar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Live Search Fitur dengan Vanilla JS
document.getElementById('searchInventory').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll('#datatable tbody tr');

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(value) ? '' : 'none';
    });
});
</script>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handler Klik tombol Modal Masuk
    $(document).on('click', '.btn-barang-masuk', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalBarangMasuk'));
        modal.show();
    });

    // Handler Klik tombol Modal Keluar
    $(document).on('click', '.btn-barang-keluar', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalBarangKeluar'));
        modal.show();
    });

    // Handler Perubahan Barang Masuk
    $('#product_masuk').on('change', function() {
        const selected = $(this).find(':selected');
        const stock = selected.data('stock');
        
        if ($(this).val()) {
            $('#stok_masuk_info').text('Stok saat ini: ' + stock);
        } else {
            $('#stok_masuk_info').text('Pilih barang untuk melihat stok saat ini.');
        }
    });

    // Handler Perubahan Barang Keluar
    $('#product_keluar').on('change', function() {
        const selected = $(this).find(':selected');
        const stock = parseInt(selected.data('stock')) || 0;
        
        if ($(this).val()) {
            $('#quantity_keluar').attr('max', stock);
            $('#stok_keluar_info').text('Maksimal barang keluar: ' + stock);
        } else {
            $('#quantity_keluar').removeAttr('max');
            $('#stok_keluar_info').text('Pilih barang untuk melihat batas stok keluar.');
        }
    });

    // Validasi Keluar Melebihi Batas Stok
    $('#formBarangKeluar').on('submit', function(e) {
        const selected = $('#product_keluar').find(':selected');
        const stock = parseInt(selected.data('stock')) || 0;
        const quantity = parseInt($('#quantity_keluar').val()) || 0;
        
        if (quantity > stock) {
            e.preventDefault();
            alert('Jumlah barang keluar tidak boleh melebihi stok tersedia.');
            return false;
        }
    });

    // Reset Form Saat Modal Ditutup
    $('#modalBarangMasuk').on('hidden.bs.modal', function() {
        $('#formBarangMasuk')[0].reset();
        $('#stok_masuk_info').text('Pilih barang untuk melihat stok saat ini.');
    });

    $('#modalBarangKeluar').on('hidden.bs.modal', function() {
        $('#formBarangKeluar')[0].reset();
        $('#quantity_keluar').removeAttr('max');
        $('#stok_keluar_info').text('Pilih barang untuk melihat batas stok keluar.');
    });
});
</script>
@endpush