@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Profil Admin</h2>
            <p class="text-muted mb-0">Kelola informasi data diri dan hak akses akun Anda.</p>
        </div>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Container Utama Berlatar Abu-Abu Sesuai Contoh Gambar --}}
    <div class="p-4 rounded-4 shadow-sm" style="background-color: #e9ecef;">
        <div class="row g-4">
            
            {{-- KARTU KIRI: Ringkasan Profil Singkat --}}
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 shadow-sm h-100">
                    <div class="card-body d-flex flex-column align-items-center text-center py-5">
                        {{-- Avatar Icon --}}
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 110px; height: 110px;">
                            <i class="bi bi-person-circle text-dark" style="font-size: 4.5rem;"></i>
                        </div>
                        
                        {{-- Nama Pengguna Aktif --}}
                        <h4 class="fw-bold text-dark mb-4">{{ Auth::user()->name }}</h4>

                        {{-- Detail Informasi Mini --}}
                        <div class="w-100 px-2 mt-2">
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="text-muted small">Email</span>
                                <span class="text-dark fw-semibold small text-break">{{ Auth::user()->email }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="text-muted small">Role</span>
                                <span class="badge bg-dark rounded-pill px-3 py-1 text-capitalize small">Admin</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <span class="text-muted small">Terdaftar sejak</span>
                                <span class="text-dark fw-semibold small">
                                    {{ Auth::user()->created_at ? Auth::user()->created_at->format('d-m-Y') : '31-1-2024' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KARTU KANAN: Form Edit Profil --}}
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm h-100">
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                {{-- Input Nama Lengkap --}}
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-2">Nama Lengkap</label>
                                    <input type="text" name="name" 
                                           class="form-control form-control-lg bg-light border-0 rounded-3 text-dark fs-6" 
                                           value="{{ old('name', Auth::user()->name) }}" 
                                           placeholder="Nama Lengkap" required>
                                </div>

                                {{-- Input Email --}}
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-2">Email</label>
                                    <input type="email" name="email" 
                                           class="form-control form-control-lg bg-light border-0 rounded-3 text-dark fs-6" 
                                           value="{{ old('email', Auth::user()->email) }}" 
                                           placeholder="Email" required>
                                </div>

                                {{-- Input Role (Disabled) --}}
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-2">Role</label>
                                    <input type="text" class="form-control form-control-lg bg-light border-0 rounded-3 text-muted fs-6" 
                                           value="Admin" disabled>
                                </div>

                                {{-- Input Tanggal Terdaftar (Disabled) --}}
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-2">Tanggal Terdaftar</label>
                                    <input type="text" class="form-control form-control-lg bg-light border-0 rounded-3 text-muted fs-6" 
                                           value="{{ Auth::user()->created_at ? Auth::user()->created_at->format('d-m-Y') : '31-1-2024' }}" disabled>
                                </div>

                                {{-- Tambahan Opsional: Ubah Password --}}
                                <div class="col-md-12 pt-2">
                                    <hr class="text-muted opacity-25">
                                    <h6 class="fw-bold text-dark mb-3 small text-uppercase tracking-wider">Ubah Password (Kosongkan jika tidak diganti)</h6>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-2">Password Baru</label>
                                    <input type="password" name="password" 
                                           class="form-control form-control-lg bg-light border-0 rounded-3 fs-6" 
                                           placeholder="Masukkan password baru">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-semibold small mb-2">Konfirmasi Password Baru</label>
                                    <input type="password" name="password_confirmation" 
                                           class="form-control form-control-lg bg-light border-0 rounded-3 fs-6" 
                                           placeholder="Ulangi password baru">
                                </div>
                            </div>

                            {{-- Tombol Aksi (Reset & Simpan) Sesuai Layout Contoh --}}
                            <div class="d-flex justify-content-center gap-3 mt-5">
                                <button type="reset" class="btn btn-secondary px-4 rounded-pill btn-lg fs-6 shadow-sm border-0" style="background-color: #adb5bd; min-width: 110px;">
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-dark px-4 rounded-pill btn-lg fs-6 shadow-sm border-0" style="background-color: #6c757d; min-width: 110px;">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection