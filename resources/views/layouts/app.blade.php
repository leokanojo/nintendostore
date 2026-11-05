<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Persediaan Barang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #1e40af, #2563eb);
            color: white;
            overflow-y: auto;
            box-shadow: 4px 0 15px rgba(0,0,0,.15);
        }

        .sidebar-header {
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
        }

        .sidebar-header small {
            opacity: .8;
        }

        .sidebar-menu {
            padding: 15px;
        }

        /* Navigasi Default (Tidak Aktif) */
        .sidebar-menu a {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 8px;
            transition: all .3s;
            font-weight: 500;
        }

        /* Efek Hover saat Kursor Menyentuh Menu */
        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(5px);
        }

        /* Status Aktif saat Laman Sedang Dibuka */
        .sidebar-menu a.active {
            background: white !important;
            color: #1e40af !important;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .submenu {
            margin-left: 10px;
        }

        .submenu a {
            padding-left: 25px;
            font-size: 0.95rem;
        }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }

        .content {
            padding: 25px;
        }

        .card {
            border-radius: 15px;
        }
    </style>
</head>
<body>

<div class="sidebar">

    <div class="sidebar-header">
        <h4>Nintendo Shop</h4>
        <small>Inventory System</small>
    </div>

    <div class="sidebar-menu">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        {{-- Master Data Dropdown --}}
        <a data-bs-toggle="collapse" href="#masterData" role="button" aria-expanded="{{ request()->routeIs('master-data.*') ? 'true' : 'false' }}">
            Master Data
        </a>

        <div class="collapse {{ request()->routeIs('master-data.*') ? 'show' : '' }} submenu" id="masterData">
            <a href="{{ route('master-data.kategori-barang.index') }}" class="{{ request()->routeIs('master-data.kategori-barang.*') ? 'active' : '' }}">
                Kategori Barang
            </a>
            <a href="{{ route('master-data.daftar-barang.index') }}" class="{{ request()->routeIs('master-data.daftar-barang.*') ? 'active' : '' }}">
                Daftar Barang
            </a>
            <a href="{{ route('master-data.manajemen-pengguna.index') }}" class="{{ request()->routeIs('master-data.manajemen-pengguna.*') ? 'active' : '' }}">
                Manajemen Pengguna
            </a>
        </div>

        {{-- Persediaan Barang Link --}}
        <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">
            Persediaan Barang
        </a>

        {{-- Laporan Link --}}
        <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            Laporan
        </a>

    </div>

</div>

<div class="main-content">

    <div class="topbar d-flex justify-content-between">
        <h5 class="mb-0 align-self-center">Sistem Persediaan Barang</h5>

        <div class="dropdown">
            <a href="#" class="text-dark text-decoration-none dropdown-toggle fw-semibold" data-bs-toggle="dropdown">
                {{ auth()->user()->name ?? 'Administrator' }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li>
                    <a class="dropdown-item small" href="{{ route('profile.edit') }}">
                        Profil Saya
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item small text-danger fw-semibold">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <div class="content">
        @yield('content')
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

</body>
</html>