<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan Halaman Utama Dashboard beserta statistiknya.
     */
    public function index()
    {
        // Statistik utama
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');

        $totalStockIn = StockTransaction::where('type', 'masuk')
            ->sum('quantity');

        $totalStockOut = StockTransaction::where('type', 'keluar')
            ->sum('quantity');

        // Stok rendah
        $lowStockCount = Product::where('stock', '<', 10)->count();

        $lowStockProducts = Product::with('category')
            ->where('stock', '<', 10)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // Stok terendah
        $topMinProducts = Product::with('category')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // Stok tertinggi
        $topMaxProducts = Product::with('category')
            ->orderBy('stock', 'desc')
            ->take(5)
            ->get();

        // Transaksi terbaru
        $latestTransactions = StockTransaction::with([
                'product.category',
                'user'
            ])
            ->latest()
            ->take(5)
            ->get();

 // DATA GRAFIK: 1 Minggu Berjalan (Senin - Minggu)
        // ==========================================
        $startOfWeek = now()->startOfWeek(); // Hari Senin jam 00:00
        $endOfWeek = now()->endOfWeek();     // Hari Minggu jam 23:59

        $weeklyTransactions = StockTransaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw("SUM(CASE WHEN type = 'masuk' THEN quantity ELSE 0 END) as total_in"),
                DB::raw("SUM(CASE WHEN type = 'keluar' THEN quantity ELSE 0 END) as total_out")
            )
            ->whereBetween('transaction_date', [$startOfWeek, $endOfWeek])
            ->groupBy(DB::raw('DATE(transaction_date)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date'); // Jadikan tanggal sebagai key array

        $chartLabels = [];
        $chartIn = [];
        $chartOut = [];

        // Looping dari Senin sampai Minggu untuk membuat data grafik konsisten
        for ($date = $startOfWeek->copy(); $date <= $endOfWeek; $date->addDay()) {
            $formattedDate = $date->toDateString();
            
            // Format label sumbu X (Contoh: Sen, 08 Jun)
            $chartLabels[] = $date->translatedFormat('D, d M'); 
            
            // Jika ada transaksi di tanggal tersebut ambil nilainya, jika kosong set ke 0
            $chartIn[] = isset($weeklyTransactions[$formattedDate]) ? (int) $weeklyTransactions[$formattedDate]->total_in : 0;
            $chartOut[] = isset($weeklyTransactions[$formattedDate]) ? (int) $weeklyTransactions[$formattedDate]->total_out : 0;
        }

        return view('dashboard.index', compact(
            'totalProducts',
            'totalStock',
            'totalStockIn',
            'totalStockOut',
            'lowStockCount',
            'lowStockProducts',
            'topMinProducts',
            'topMaxProducts',
            'latestTransactions',
            'chartLabels',
            'chartIn',
            'chartOut'
        ));
    }

    /**
     * Menampilkan halaman Profil Admin.
     * Mengarah ke resources/views/profil/index.blade.php
     */
    public function editProfile()
    {
        return view('profil.index');
    }

    /**
     * Memproses pembaharuan data Profil Admin.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validasi input form profil
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Update data dasar
        $user->name = $request->name;
        $user->email = $request->email;

        // Update password jika user mengetikkan sesuatu pada input password baru
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
    }
}