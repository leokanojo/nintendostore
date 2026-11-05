@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">

    {{-- Bagian Atas / Header Halaman --}}
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="text-primary fw-bold m-0">📊 Laporan Persediaan</h2>
        </div>
        <p class="text-secondary m-0">Lihat laporan transaksi barang masuk dan barang keluar berdasarkan periode tanggal.</p>
    </div>

    {{-- Kotak Filter Laporan --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="fw-bold m-0 text-dark">Filter Laporan</h5>
            <p class="mb-0 text-muted small">Pilih jenis transaksi dan rentang tanggal untuk menyaring laporan data.</p>
        </div>
        <div class="card-body px-4 pb-4 pt-0">
            <form action="{{ route('reports.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Jenis Transaksi</label>
                        <select name="type" class="form-select form-select-sm text-dark">
                            <option value="">Semua Transaksi</option>
                            <option value="masuk" {{ request('type') == 'masuk' ? 'selected' : '' }}>Barang Masuk</option>
                            <option value="keluar" {{ request('type') == 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tanggal Awal</label>
                        <input type="date" name="start_date" class="form-control form-control-sm text-dark" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control form-control-sm text-dark" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm fw-semibold w-100">Filter</button>
                            <a href="{{ route('reports.index') }}" class="btn btn-light border btn-sm w-100">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan / Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <p class="mb-1 text-secondary small fw-bold text-uppercase">Total Transaksi</p>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($totalTransactions) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <p class="mb-1 text-secondary small fw-bold text-uppercase">Total Barang Masuk</p>
                    <h3 class="mb-0 fw-bold text-success">+ {{ number_format($totalIn) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <p class="mb-1 text-secondary small fw-bold text-uppercase">Total Barang Keluar</p>
                    <h3 class="mb-0 fw-bold text-danger">- {{ number_format($totalOut) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Kotak Master Data Utama --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3 px-4">
            <div>
                <h4 class="fw-bold m-0 text-dark">Data Laporan Transaksi</h4>
                <p class="mb-0 text-muted small">Daftar rekaman mutasi log pergudangan internal.</p>
            </div>
        </div>
        
        {{-- Area Live Search --}}
        <div class="px-4 pb-2">
            <div class="row g-2 justify-content-between">
                <div class="col-md-5">
                    <input type="text" 
                           id="searchLaporan" 
                           class="form-control form-control-sm text-dark" 
                           placeholder="Cari kode, nama produk, petugas, atau catatan... (Instan)">
                </div>
                <div class="col-md-6 text-end" id="wrapperExportButtons">
                    {{-- Target Inject Tombol DataTables Export --}}
                </div>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="card-body p-0 mt-2">
            @if (request('type') || request('start_date') || request('end_date'))
                <div class="mx-4 my-2 alert alert-light border small py-2 text-dark">
                    <strong>Filter Aktif:</strong> 
                    Jenis: <span class="badge bg-secondary">{{ request('type') ? 'Barang '.ucfirst(request('type')) : 'Semua Transaksi' }}</span> | 
                    Periode: <span class="badge bg-secondary">{{ request('start_date') ?: 'Awal' }} s/d {{ request('end_date') ?: 'Akhir' }}</span>
                </div>
            @endif

            <div class="table-responsive">
                <table id="laporanTable" class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th width="5%" class="ps-4 py-3">No</th>
                            <th width="10%">Tanggal</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Petugas</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td>{{ $transaction->transaction_date ? $transaction->transaction_date->format('d-m-Y') : '-' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $transaction->product->code ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $transaction->product->name ?? '-' }}</span>
                                </td>
                                <td>{{ $transaction->product->category->name ?? '-' }}</td>
                                <td>
                                    @if ($transaction->type === 'masuk')
                                        <span class="badge bg-success px-2 py-1">Barang Masuk</span>
                                    @else
                                        <span class="badge bg-danger px-2 py-1">Barang Keluar</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="{{ $transaction->type === 'masuk' ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($transaction->quantity) }}
                                    </strong>
                                    <small class="text-muted">{{ $transaction->product->unit ?? '' }}</small>
                                </td>
                                <td>{{ $transaction->user->name ?? '-' }}</td>
                                <td>
                                    @if ($transaction->note)
                                        <span class="text-dark">{{ Str::limit($transaction->note, 50) }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Tidak ada data transaksi sesuai kriteria filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Navigasi Paginasi Bawaan DataTables (Akan muncul di sini jika data > length) --}}
            <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center" id="tableFooterPagination"></div>
        

        </div>
</div> {{-- Penutup card-body area tabel data --}}

        {{-- BAGIAN PAGINASI CUSTOM --}}
        @if($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $transactions->total() > 0)
            <div class="d-flex flex-column align-items-center justify-content-center gap-2 px-4 py-3 bg-light border-top rounded-bottom-3">
                
                {{-- Row 1: Tombol Navigasi Paginasi --}}
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

                {{-- Row 2: Keterangan Data entries --}}
                <div class="text-muted small text-center w-100 mt-1">
                    Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} entries
                </div>

            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- DataTables Export Buttons Resource --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.jQuery && $.fn.DataTable) {
        // Inisialisasi DataTable Engine
        const table = $('#laporanTable').DataTable({
            pageLength: 25,
            lengthMenu: [
                [25, 50, 100, -1],
                [25, 50, 100, 'Semua']
            ],
            // Kostumisasi DOM agar kontrol bawaan tersembunyi & dipindahkan ke container buatan sendiri
            dom: 'Brtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '📁 Export Excel',
                    className: 'btn btn-success btn-sm fw-semibold rounded-2 me-1 border-0',
                    title: 'Laporan_Keluar_Masuk_Barang'
                },
                {
                    extend: 'pdfHtml5',
                    text: '📄 Export PDF',
                    className: 'btn btn-danger btn-sm fw-semibold rounded-2 me-1 border-0',
                    title: 'Laporan_Keluar_Masuk_Barang',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    text: '🖨️ Print',
                    className: 'btn btn-dark btn-sm fw-semibold rounded-2 border-0',
                    title: 'Laporan Keluar Masuk Barang'
                }
            ]
        });

        // Pindahkan tombol export DataTables ke wadah kolom kanan atas agar seimbang
        table.buttons().container().appendTo('#wrapperExportButtons');

        // Handler Event Live Search Pengganti yang tidak macet
        document.getElementById('searchLaporan').addEventListener('keyup', function() {
            table.search(this.value).draw();
        });
    }
});
</script>
@endpush