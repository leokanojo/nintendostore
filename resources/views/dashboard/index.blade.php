@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- BARIS UTAMA: Statistik Samping (Kiri) & Grafik (Kanan) --}}
    <div class="row mb-4 align-items-stretch">
        
        {{-- Sisi Kiri: 4 Mini Cards Disusun Vertikal --}}
        <div class="col-md-3 d-flex flex-column gap-3" id="statsContainer">
            {{-- Total Barang --}}
            <div class="card border-0 shadow-sm text-white flex-fill"
                style="background: linear-gradient(135deg,#2563eb,#3b82f6);">
                <div class="card-body p-3">
                    <h6 class="small mb-1">Total Barang</h6>
                    <h2 class="fw-bold m-0">{{ $totalProducts }}</h2>
                </div>
            </div>

            {{-- Total Stock --}}
            <div class="card border-0 shadow-sm text-white flex-fill"
                style="background: linear-gradient(135deg,#0891b2,#06b6d4);">
                <div class="card-body p-3">
                    <h6 class="small mb-1">Total Stock</h6>
                    <h2 class="fw-bold m-0">{{ $totalStock }}</h2>
                </div>
            </div>

            {{-- Barang Masuk --}}
            <div class="card border-0 shadow-sm text-white flex-fill"
                style="background: linear-gradient(135deg,#16a34a,#22c55e);">
                <div class="card-body p-3">
                    <h6 class="small mb-1">Barang Masuk</h6>
                    <h2 class="fw-bold m-0">{{ $totalStockIn }}</h2>
                </div>
            </div>

            {{-- Barang Keluar --}}
            <div class="card border-0 shadow-sm text-white flex-fill"
                style="background: linear-gradient(135deg,#dc2626,#ef4444);">
                <div class="card-body p-3">
                    <h6 class="small mb-1">Barang Keluar</h6>
                    <h2 class="fw-bold m-0">{{ $totalStockOut }}</h2>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Grafik Transaksi Utama --}}
        <div class="col-md-9">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="fw-bold mb-3 small text-muted">Grafik Transaksi Minggu Ini</h5>
                    {{-- Wrapper pembatas tinggi grafik agar sejajar kotak merah --}}
                    <div class="flex-grow-1" style="position: relative; min-height: 280px; max-height: 310px;">
                        <canvas id="inventoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- BARIS KEDUA: Tabel Informasi --}}
    <div class="row">

        <div class="col-lg-6">

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <strong>5 Stok Terendah</strong>
                </div>
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topMinProducts as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->category->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ $item->stock }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <strong>5 Stok Tertinggi</strong>
                </div>
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topMaxProducts as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->category->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $item->stock }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="col-lg-6">

            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <strong>Transaksi Terbaru</strong>
                </div>
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>User</th>
                                <th>Qty</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestTransactions as $trx)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y') }}
                                    </td>
                                    <td>{{ $trx->product->name ?? '-' }}</td>
                                    <td>{{ $trx->user->name ?? '-' }}</td>
                                    <td>{{ $trx->quantity }}</td>
                                    <td>
                                        @if($trx->type == 'masuk')
                                            <span class="badge bg-success">Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Keluar</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada transaksi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('inventoryChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [
            {
                label: 'Barang Masuk',
                data: {!! json_encode($chartIn) !!},
                backgroundColor: '#2563eb',
                borderRadius: 6
            },
            {
                label: 'Barang Keluar',
                data: {!! json_encode($chartOut) !!},
                backgroundColor: '#60a5fa',
                borderRadius: 6
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false, // Wajib false agar mengikuti container HTML
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                min: 0,
                max: 20,       // Mengunci nilai sumbu Y maksimal di angka 20
                ticks: {
                    stepSize: 1 // Skala grafik naik satu per satu (1, 2, 3, ... 20)
                }
            }
        }
    }
});
</script>

@endpush