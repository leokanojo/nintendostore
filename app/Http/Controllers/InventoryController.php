<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name', 'asc')
            ->get();

        $transactions = StockTransaction::with(['product.category', 'user'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(5) // Membatasi 5 baris per halaman
            ->withQueryString(); // Menjaga parameter URL jika nanti ada filter

        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');

        $lowStockCount = Product::whereColumn('stock', '<=', 'minimum_stock')
            ->where('stock', '>', 0)
            ->count();
$emptyStockCount = Product::where('stock', 0)->count();
 return view('inventory.index', compact(
 'products',
 'transactions',
 'totalProducts',
 'totalStock',
 'lowStockCount',
 'emptyStockCount'
 ));
 }
 public function storeTransaction(Request $request)
 {
 $request->validate([
 'product_id' => ['required', 'exists:products,id'],
 'type' => ['required', 'in:masuk,keluar'],
 'quantity' => ['required', 'integer', 'min:1'],
 'transaction_date' => ['required', 'date'],
 'note' => ['nullable', 'string'],
 ], [
 'product_id.required' => 'Barang wajib dipilih.',
 'product_id.exists' => 'Barang tidak valid.',
 'type.required' => 'Jenis transaksi wajib dipilih.',
 'type.in' => 'Jenis transaksi tidak valid.',
 'quantity.required' => 'Jumlah transaksi wajib diisi.',
 'quantity.integer' => 'Jumlah transaksi harus berupa angka.',
 'quantity.min' => 'Jumlah transaksi minimal 1.',
 'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
 'transaction_date.date' => 'Tanggal transaksi tidak valid.',
 ]);
 DB::transaction(function () use ($request) {
 $product = Product::lockForUpdate()->findOrFail($request->product_id);
 $quantity = (int) $request->quantity;
 $currentStock = (int) $product->stock;
 if ($request->type === 'keluar' && $quantity > $currentStock) {
 throw ValidationException::withMessages([
 'quantity' => 'Jumlah barang keluar tidak boleh melebihi stok tersedia.',
 ]);
 }
 if ($request->type === 'masuk') {
 $stockAfter = $currentStock + $quantity;
 } else {
 $stockAfter = $currentStock - $quantity;
 }
 StockTransaction::create([
 'product_id' => $product->id,
 'user_id' => Auth::id(),
 'type' => $request->type,
 'quantity' => $quantity,
 'stock_after' => $stockAfter,
 'transaction_date' => $request->transaction_date,
 'note' => $request->note,
 ]);
 $product->update([
 'stock' => $stockAfter,
 ]);
 });
 return redirect()
 ->route('inventory.index')
 ->with('success', 'Transaksi persediaan berhasil disimpan.');
 }
}
