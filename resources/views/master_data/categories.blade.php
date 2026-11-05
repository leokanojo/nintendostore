@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">

    {{-- Bagian Atas / Header Halaman --}}
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="text-primary fw-bold m-0">📁 Kategori Barang</h2>
        </div>
        <p class="text-secondary m-0">Kelola kategori untuk pengelompokan produk dan pembagian rumpun barang master.</p>
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
                <h4 class="fw-bold m-0 text-dark">Master Kategori Barang</h4>
                <p class="mb-0 text-muted small">Kelola data rumpun kategori produk sistem.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm fw-semibold px-3 btn-create-kategori">
                + Tambah Kategori
            </button>
        </div>
        
        {{-- AREA CARI DATA & SORTING --}}
        <div class="px-4 pb-2">
            <form action="{{ route('master-data.kategori-barang.index') }}" method="GET">
                <div class="row g-2 justify-content-between">
                    {{-- Input Pencarian --}}
                    <div class="col-md-5">
                        <input type="text" 
                               id="searchKategori"
                               name="search" 
                               value="{{ request('search') }}" 
                               class="form-control form-control-sm text-dark" 
                               placeholder="Cari kategori... (Tekan Enter)">
                    </div>
                    
                    {{-- Dropdown Sorting --}}
                    <div class="col-md-2 text-end">
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
                            <th width="10%" class="ps-4 py-3">No</th>
                            <th>Nama Kategori</th>
                            <th width="30%">Tanggal Dibuat</th>
                            <th width="25%" class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($categories as $category)
                            <tr>
                                {{-- Menyesuaikan nomor baris agar kontinu saat pindah page --}}
                                <td class="ps-4">{{ $loop->iteration + ($categories->firstItem() - 1) }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $category->name }}</span>
                                </td>
                                <td>{{ $category->created_at ? $category->created_at->format('d-m-Y H:i') : '-' }}</td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-1">
                                        {{-- Tombol Detail --}}
                                        <button type="button" 
                                            class="btn btn-sm btn-info text-white px-2 py-1 btn-detail-kategori"
                                            style="font-size: 11px; border: none;"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ e($category->name) }}"
                                            data-created-at="{{ $category->created_at ? $category->created_at->format('d-m-Y H:i') : '-' }}"
                                            data-updated-at="{{ $category->updated_at ? $category->updated_at->format('d-m-Y H:i') : '-' }}">
                                            Detail
                                        </button>

                                        {{-- Tombol Edit --}}
                                        <button type="button" 
                                            class="btn btn-sm btn-warning text-white px-2 py-1 btn-edit-kategori"
                                            style="font-size: 11px; border: none;"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ e($category->name) }}">
                                            Edit
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('master-data.kategori-barang.destroy', $category->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger px-2 py-1" style="font-size: 11px; border: none;">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada data kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

{{-- BAGIAN PAGINASI CUSTOM (Tombol di Tengah, Keterangan di Bawahnya & di Tengah) --}}
@if($categories instanceof \Illuminate\Pagination\LengthAwarePaginator && $categories->total() > 0)
    <div class="d-flex flex-column align-items-center justify-content-center gap-2 px-4 py-3 bg-white border-top">
        
        {{-- Row 1: Tombol Navigasi Paginasi (Paling Atas & Ditengah) --}}
        <div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm m-0 justify-content-center">
                    {{-- Tombol Previous --}}
                    @if ($categories->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $categories->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    {{-- Nomor Halaman --}}
                    @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $categories->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    {{-- Tombol Next --}}
                    @if ($categories->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $categories->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>

        {{-- Row 2: Keterangan Data entries (Di Bawah & Ditengah) --}}
        <div class="text-muted small text-center w-100 mt-1">
            Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} entries
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
<div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formKategori" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="" disabled>
                
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="modalKategoriLabel">Form Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div id="detailKategoriInfo" class="alert alert-info d-none mb-3 border-0 small text-dark">
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

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Kategori <span class="text-danger req-mark">*</span></label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" placeholder="Contoh: Elektronik / Konsol" required>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light border btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnSubmitKategori">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const storeUrl = "{{ route('master-data.kategori-barang.store') }}";
    const updateUrlTemplate = "{{ route('master-data.kategori-barang.update', ':id') }}";
    
    const $modal = $('#modalKategori');
    const $form = $('#formKategori');
    const $method = $('#formMethod');
    const $title = $('#modalKategoriLabel');
    const $btnSubmit = $('#btnSubmitKategori');
    
    const $name = $('#name');
    const $reqMark = $('.req-mark');
    const $detailInfo = $('#detailKategoriInfo');
    const $detailCreatedAt = $('#detailCreatedAt');
    const $detailUpdatedAt = $('#detailUpdatedAt');

    function resetModalKategori() {
        $form[0].reset();
        $form.attr('action', storeUrl);
        $method.prop('disabled', true).val('');
        
        $name.prop('readonly', false);
        $reqMark.removeClass('d-none');
        $detailInfo.addClass('d-none');
        
        $title.text('Form Kategori');
        $btnSubmit.removeClass('d-none').text('Simpan Kategori');
    }

    $('.btn-create-kategori').on('click', function() {
        resetModalKategori();
        $title.text('Tambah Kategori');
        $modal.modal('show');
    });

    $(document).on('click', '.btn-edit-kategori', function() {
        resetModalKategori();
        const id = $(this).data('id');
        const updateUrl = updateUrlTemplate.replace(':id', id);
        
        $form.attr('action', updateUrl);
        $method.prop('disabled', false).val('PUT');
        
        $name.val($(this).data('name'));
        
        $title.text('Edit Kategori');
        $btnSubmit.text('Simpan Perubahan');
        $modal.modal('show');
    });

    $(document).on('click', '.btn-detail-kategori', function() {
        resetModalKategori();
        
        $name.val($(this).data('name')).prop('readonly', true);
        $reqMark.addClass('d-none');
        
        $detailCreatedAt.text($(this).data('created-at'));
        $detailUpdatedAt.text($(this).data('updated-at'));
        $detailInfo.removeClass('d-none');
        
        $title.text('Detail Kategori');
        $btnSubmit.addClass('d-none');
        $modal.modal('show');
    });

    $modal.on('hidden.bs.modal', function() {
        resetModalKategori();
    });
});
</script>

<script>
// Live Search untuk halaman aktif saat ini
document.getElementById('searchKategori').addEventListener('keyup', function() {
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