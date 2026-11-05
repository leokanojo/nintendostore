@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    {{-- Bagian Atas / Header Halaman --}}
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="text-primary fw-bold m-0">📦 Daftar Barang</h2>
        </div>
        <p class="text-secondary m-0">Kelola master barang, stok awal, dan batas minimum persediaan.</p>
    </div>

    {{-- Flash Message/Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Error --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Kotak Master Data Utama --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3 px-4">
            <div>
                <h4 class="fw-bold m-0 text-dark">Master Data Barang</h4>
                <p class="mb-0 text-muted small">Kelola data barang seperti kode barang, kategori, stok, dan stok minimum.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm fw-semibold px-3 btn-create-barang">
                + Tambah Barang
            </button>
        </div>
        
        {{-- AREA CARI DATA & SORTING --}}
        <div class="px-4 pb-2">
            <form method="GET" action="{{ route('master-data.daftar-barang.index') }}">
                <div class="row g-2 justify-content-between">
                    {{-- Input Pencarian --}}
                    <div class="col-md-5">
                        <input type="text" 
                               id="searchBarang"
                               name="search" 
                               value="{{ request('search') }}" 
                               class="form-control form-control-sm text-dark" 
                               placeholder="Cari kode, nama barang, atau kategori... (Tekan Enter)">
                    </div>

                    {{-- Dropdown Sorting --}}
                    <div class="col-md-2 text-end">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <select name="sort" class="form-select form-select-sm text-dark" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="az" {{ request('sort') == 'az' ? 'selected' : '' }}>A - Z</option>
                            <option value="za" {{ request('sort') == 'za' ? 'selected' : '' }}>Z - A</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel Data --}}
        <div class="card-body p-0 mt-2">
            <div class="table-responsive">
                <table id="datatable" class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th width="5%" class="ps-4 py-3">No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Minimum</th>
                            <th>Status</th>
                            <th width="25%" class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($products as $product)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $product->code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    @if ($product->description)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($product->description, 60) }}</small>
                                    @endif
                                </td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>{{ number_format($product->stock) }}</td>
                                <td>{{ number_format($product->minimum_stock) }}</td>
                                <td>
                                    @if ($product->stock <= $product->minimum_stock)
                                        <span class="badge bg-danger">Stok Minimum</span>
                                    @else
                                        <span class="badge bg-success">Aman</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-1">
                                        {{-- BUTTON DETAIL (Meniru Gaya Halaman Pengguna Anda) --}}
                                        <button type="button" 
                                            class="btn btn-sm btn-info text-white px-2 py-1 btn-detail-barang"
                                            style="font-size: 11px; border: none;"
                                            data-category-id="{{ $product->category_id }}"
                                            data-code="{{ e($product->code) }}"
                                            data-name="{{ e($product->name) }}"
                                            data-stock="{{ $product->stock }}"
                                            data-minimum-stock="{{ $product->minimum_stock }}"
                                            data-description="{{ e($product->description ?? 'Tidak ada deskripsi.') }}"
                                            data-created-at="{{ $product->created_at ? $product->created_at->format('d-m-Y H:i') : '-' }}"
                                            data-updated-at="{{ $product->updated_at ? $product->updated_at->format('d-m-Y H:i') : '-' }}">
                                            Detail
                                        </button>

                                        {{-- BUTTON EDIT --}}
                                        <button type="button" 
                                            class="btn btn-sm btn-warning text-white px-2 py-1 btn-edit-barang"
                                            style="font-size: 11px; border: none;"
                                            data-id="{{ $product->id }}"
                                            data-category-id="{{ $product->category_id }}"
                                            data-code="{{ e($product->code) }}"
                                            data-name="{{ e($product->name) }}"
                                            data-stock="{{ $product->stock }}"
                                            data-minimum-stock="{{ $product->minimum_stock }}"
                                            data-description="{{ e($product->description) }}">
                                            Edit
                                        </button>

                                        {{-- BUTTON HAPUS --}}
                                        <form action="{{ route('master-data.daftar-barang.destroy', $product->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger px-2 py-1" style="font-size: 11px; border: none;" onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- BAGIAN PAGINASI CUSTOM --}}
@if($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->total() > 0)
    <div class="d-flex flex-column align-items-center justify-content-center gap-2 px-4 py-3 bg-white border-top">
        
        {{-- Row 1: Tombol Navigasi Paginasi --}}
        <div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm m-0 justify-content-center">
                    {{-- Tombol Previous --}}
                    @if ($products->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $products->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    {{-- Nomor Halaman --}}
                    @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $products->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    {{-- Tombol Next --}}
                    @if ($products->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $products->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>

        {{-- Row 2: Keterangan Data entries --}}
        <div class="text-muted small text-center w-100 mt-1">
            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries
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



{{-- MODAL FORM SERBAGUNA (CREATE / UPDATE / DETAIL) --}}
<div class="modal fade" id="modalBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formBarang" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="" disabled>
                
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="modalBarangLabel">Form Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    {{-- Detail Timestamp Info (Meniru Laman Pengguna) --}}
                    <div id="detailBarangInfo" class="alert alert-info d-none mb-3 border-0 small text-dark">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block">Dibuat</small>
                                <strong id="detailCreatedAt">-</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Diupdate</small>
                                <strong id="detailUpdatedAt">-</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Kategori Barang <span class="text-danger req-mark">*</span></label>
                            <select name="category_id" id="category_id" class="form-select form-select-sm" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Kode Barang <span class="text-danger req-mark">*</span></label>
                            <input type="text" name="code" id="code" class="form-control form-control-sm" placeholder="Contoh: BRG-001" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold">Nama Barang <span class="text-danger req-mark">*</span></label>
                            <input type="text" name="name" id="name" class="form-control form-control-sm" placeholder="Contoh: Baja Ringan C75" required>
                        </div>
                        <div class="col-md-6 mb-3" id="stockSection">
                            <label class="form-label small fw-bold">Stok Awal <span class="text-danger req-mark">*</span></label>
                            <input type="number" name="stock" id="stock" class="form-control form-control-sm" min="0" value="0" required>
                        </div>
                        <div class="col-md-6 mb-3" id="minStockSection">
                            <label class="form-label small fw-bold">Stok Minimum <span class="text-danger req-mark">*</span></label>
                            <input type="number" name="minimum_stock" id="minimum_stock" class="form-control form-control-sm" min="0" value="10" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label small fw-bold">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control form-control-sm" rows="3" placeholder="Tulis deskripsi singkat barang"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light border btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitBarang" class="btn btn-sm btn-primary">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection {{-- Tag penutup section halaman utama diletakkan di sini sebelum push scripts --}}

@push('scripts')
<script>
$(document).ready(function() {
    const storeUrl = "{{ route('master-data.daftar-barang.store') }}";
    const updateUrlTemplate = "{{ route('master-data.daftar-barang.update', ':id') }}";
    
    const $modal = $('#modalBarang');
    const $form = $('#formBarang');
    const $method = $('#formMethod');
    const $title = $('#modalBarangLabel');
    const $btnSubmit = $('#btnSubmitBarang');
    
    const $category = $('#category_id');
    const $code = $('#code');
    const $name = $('#name');
    const $stock = $('#stock');
    const $minimumStock = $('#minimum_stock');
    const $description = $('#description');
    
    const $reqMark = $('.req-mark');
    const $detailInfo = $('#detailBarangInfo');
    const $detailCreatedAt = $('#detailCreatedAt');
    const $detailUpdatedAt = $('#detailUpdatedAt');

    // Fungsi Reset State Modal ke Setelan Awal (Tambah Data)
    function resetModalBarang() {
        $form[0].reset();
        $form.attr('action', storeUrl);
        $method.prop('disabled', true).val('');
        
        // Kembalikan status readonly/disabled ke false
        $category.prop('disabled', false);
        $code.prop('readonly', false);
        $name.prop('readonly', false);
        $stock.prop('readonly', false);
        $minimumStock.prop('readonly', false);
        $description.prop('readonly', false);
        
        $reqMark.removeClass('d-none');
        $detailInfo.addClass('d-none');
        
        $title.text('Form Barang');
        $btnSubmit.removeClass('d-none').text('Simpan Barang');
    }

    // Handler Aksi Tambah Barang
    $('.btn-create-barang').on('click', function() {
        resetModalBarang();
        $title.text('Tambah Barang');
        $modal.modal('show');
    });

    // Handler Aksi Edit Barang
    $(document).on('click', '.btn-edit-barang', function() {
        resetModalBarang();
        const id = $(this).data('id');
        const updateUrl = updateUrlTemplate.replace(':id', id);
        
        $form.attr('action', updateUrl);
        $method.prop('disabled', false).val('PUT');
        
        $category.val($(this).data('category-id'));
        $code.val($(this).data('code'));
        $name.val($(this).data('name'));
        $stock.val($(this).data('stock'));
        $minimumStock.val($(this).data('minimum-stock'));
        $description.val($(this).data('description'));
        
        $title.text('Edit Barang');
        $btnSubmit.text('Simpan Perubahan');
        $modal.modal('show');
    });

    // Handler Aksi Detail Barang (Meniru Persis Logika Laman Pengguna)
    $(document).on('click', '.btn-detail-barang', function() {
        resetModalBarang();
        
        $category.val($(this).data('category-id')).prop('disabled', true);
        $code.val($(this).data('code')).prop('readonly', true);
        $name.val($(this).data('name')).prop('readonly', true);
        $stock.val($(this).data('stock')).prop('readonly', true);
        $minimumStock.val($(this).data('minimum-stock')).prop('readonly', true);
        $description.val($(this).data('description')).prop('readonly', true);
        
        $reqMark.addClass('d-none');
        
        $detailCreatedAt.text($(this).data('created-at'));
        $detailUpdatedAt.text($(this).data('updated-at'));
        $detailInfo.removeClass('d-none');
        
        $title.text('Detail Informasi Barang');
        $btnSubmit.addClass('d-none');
        $modal.modal('show');
    });

    $modal.on('hidden.bs.modal', function() {
        resetModalBarang();
    });
});
</script>

<script>
// Live Search Fitur (Client Side)
document.getElementById('searchBarang')?.addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll('#datatable tbody tr');

    rows.forEach(row => {
        if(row.cells.length > 1) {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        }
    });
});
</script>
@endpush