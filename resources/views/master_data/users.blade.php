@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    
    {{-- Bagian Atas / Header Halaman --}}
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="text-primary fw-bold m-0" style="color: #0d6efd;">👥 Daftar Pengguna</h2>
        </div>
        <p class="text-secondary m-0">Kelola master data pengguna, registrasi akun baru, dan hak akses login sistem.</p>
    </div>

    {{-- Flash Message/Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Kotak Master Data Utama --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 py-3 px-4">
            <div>
                <h4 class="fw-bold m-0 text-dark">Master Data Pengguna</h4>
                <p class="mb-0 text-muted small">Kelola data akun seperti nama pengguna, email, dan password akses.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm fw-semibold px-3 btn-create-user" style="background-color: #0d6efd; border: none;">
                + Tambah Pengguna
            </button>
        </div>
        
        {{-- AREA CARI DATA & SORTING (Bungkus dengan form GET ke index) --}}
        <div class="px-4 pb-2">
            <form action="{{ route('master-data.manajemen-pengguna.index') }}" method="GET">
                <div class="row g-2 justify-content-between">
                    {{-- Input Pencarian --}}
                    <div class="col-md-5">
                        <input type="text" 
                               id="searchUser"
                               name="search" 
                               value="{{ request('search') }}" 
                               class="form-control form-control-sm text-dark" 
                               placeholder="Cari nama atau email pengguna... (Tekan Enter)">
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
                            <th width="5%" class="ps-4 py-3">No</th>
                            <th width="25%">Nama Pengguna</th>
                            <th width="30%">Email</th>
                            <th width="20%">Tanggal Dibuat</th>
                            <th width="20%" class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse ($users as $user)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $user->name }}</span>
                                    @if (auth()->id() === $user->id)
                                        <span class="badge bg-success ms-1" style="font-size: 10px;">Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at ? $user->created_at->format('d-m-Y H:i') : '-' }}</td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" 
                                            class="btn btn-sm btn-info text-white px-2 py-1 btn-detail-user"
                                            style="font-size: 11px; background-color: #17a2b8; border: none;"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ e($user->name) }}"
                                            data-email="{{ e($user->email) }}"
                                            data-created-at="{{ $user->created_at ? $user->created_at->format('d-m-Y H:i') : '-' }}"
                                            data-updated-at="{{ $user->updated_at ? $user->updated_at->format('d-m-Y H:i') : '-' }}">
                                            Detail
                                        </button>
                                        <button type="button" 
                                            class="btn btn-sm btn-warning text-white px-2 py-1 btn-edit-user"
                                            style="font-size: 11px; background-color: #ffc107; border: none;"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ e($user->name) }}"
                                            data-email="{{ e($user->email) }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('master-data.manajemen-pengguna.destroy', $user->id) }}" 
                                            method="POST" 
                                            class="d-inline"
                                            onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger px-2 py-1" style="font-size: 11px; background-color: #dc3545; border: none;">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- BAGIAN PAGINASI CUSTOM (Tombol di Tengah, Keterangan di Bawahnya & di Tengah) --}}
@if($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->total() > 0)
    <div class="d-flex flex-column align-items-center justify-content-center gap-2 px-4 py-3 bg-white border-top">
        
        {{-- Row 1: Tombol Navigasi Paginasi (Paling Atas & Ditengah) --}}
        <div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm m-0 justify-content-center">
                    {{-- Tombol Previous --}}
                    @if ($users->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    {{-- Nomor Halaman --}}
                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $users->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    {{-- Tombol Next --}}
                    @if ($users->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>

        {{-- Row 2: Keterangan Data entries (Di Bawah & Ditengah) --}}
        <div class="text-muted small text-center w-100 mt-1">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
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

{{-- MODAL FORM (CREATE / UPDATE / DETAIL) --}}
<div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formUser" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="" disabled>
                
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="modalUserLabel">Form Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4">
                    <div id="detailUserInfo" class="alert alert-info d-none mb-3 border-0 small text-dark">
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
                        <label class="form-label small fw-bold">Nama Pengguna <span class="text-danger req-mark">*</span></label>
                        <input type="text" name="name" id="name" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email <span class="text-danger req-mark">*</span></label>
                        <input type="email" name="email" id="email" class="form-control form-control-sm" required>
                    </div>
                    
                    <div id="passwordSection" class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Password <span class="text-danger req-mark">*</span></label>
                            <input type="password" name="password" id="password" class="form-control form-control-sm">
                            <small class="text-muted password-help mt-1 d-block" style="font-size: 10px;"></small>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Konfirmasi Password <span class="text-danger req-mark">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light border btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btnSubmitUser">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const storeUrl = "{{ route('master-data.manajemen-pengguna.store') }}";
    const updateUrlTemplate = "{{ route('master-data.manajemen-pengguna.update', ':id') }}";
    
    const $modal = $('#modalUser');
    const $form = $('#formUser');
    const $method = $('#formMethod');
    const $title = $('#modalUserLabel');
    const $btnSubmit = $('#btnSubmitUser');
    
    const $name = $('#name');
    const $email = $('#email');
    const $password = $('#password');
    const $passwordConfirmation = $('#password_confirmation');
    
    const $passwordSection = $('#passwordSection');
    const $reqMark = $('.req-mark');
    const $passwordHelp = $('.password-help');
    const $detailInfo = $('#detailUserInfo');
    const $detailCreatedAt = $('#detailCreatedAt');
    const $detailUpdatedAt = $('#detailUpdatedAt');

    function resetModalUser() {
        $form[0].reset();
        $form.attr('action', storeUrl);
        $method.prop('disabled', true).val('');
        
        $name.prop('readonly', false);
        $email.prop('readonly', false);
        $password.prop('readonly', false);
        $passwordConfirmation.prop('readonly', false);
        
        $passwordSection.removeClass('d-none');
        $reqMark.removeClass('d-none');
        $password.prop('required', true);
        $passwordConfirmation.prop('required', true);
        
        $passwordHelp.text('');
        $password.attr('placeholder', 'Min 6 karakter');
        $passwordConfirmation.attr('placeholder', 'Ulangi password');
        $detailInfo.addClass('d-none');
        
        $title.text('Form Pengguna');
        $btnSubmit.removeClass('d-none').text('Simpan');
    }

    $('.btn-create-user').on('click', function() {
        resetModalUser();
        $title.text('Tambah Pengguna');
        $modal.modal('show');
    });

    $(document).on('click', '.btn-edit-user', function() {
        resetModalUser();
        const id = $(this).data('id');
        const updateUrl = updateUrlTemplate.replace(':id', id);
        
        $form.attr('action', updateUrl);
        $method.prop('disabled', false).val('PUT');
        
        $name.val($(this).data('name'));
        $email.val($(this).data('email'));
        
        $password.prop('required', false);
        $passwordConfirmation.prop('required', false);
        $('#passwordSection .req-mark').addClass('d-none');
        
        $passwordHelp.text('Kosongkan jika tidak diubah.');
        $password.attr('placeholder', 'Kosongkan jika tak diubah');
        $passwordConfirmation.attr('placeholder', 'Kosongkan jika tak diubah');
        
        $title.text('Edit Pengguna');
        $btnSubmit.text('Simpan Perubahan');
        $modal.modal('show');
    });

    $(document).on('click', '.btn-detail-user', function() {
        resetModalUser();
        
        $name.val($(this).data('name')).prop('readonly', true);
        $email.val($(this).data('email')).prop('readonly', true);
        
        $passwordSection.addClass('d-none');
        $reqMark.addClass('d-none');
        
        $detailCreatedAt.text($(this).data('created-at'));
        $detailUpdatedAt.text($(this).data('updated-at'));
        $detailInfo.removeClass('d-none');
        
        $title.text('Detail Pengguna');
        $btnSubmit.addClass('d-none');
        $modal.modal('show');
    });

    $modal.on('hidden.bs.modal', function() {
        resetModalUser();
    });
});
</script>

<script>
// Opsional: Live Search Tambahan (Client Side) untuk performa instan tanpa reload saat mengetik
document.getElementById('searchUser').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll('#datatable tbody tr');

    rows.forEach(row => {
        // Jangan sembunyikan jika baris kosong ("Belum ada data")
        if(row.cells.length > 1) {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        }
    });
});
</script>
@endpush
@endsection