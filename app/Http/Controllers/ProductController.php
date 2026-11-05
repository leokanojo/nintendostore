<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
{
    // 1. Inisialisasi Query dengan Relasi
    $query = Product::with('category')->withCount('stockTransactions');

    // 2. Fitur Filter Pencarian (Search)
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhereHas('category', function($catQuery) use ($search) {
                  $catQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    // 3. Fitur Pengurutan (Sort)
    $sort = $request->get('sort', 'latest'); // default 'latest' jika tidak memilih
    switch ($sort) {
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
        case 'az':
            $query->orderBy('name', 'asc');
            break;
        case 'za':
            $query->orderBy('name', 'desc');
            break;
        case 'latest':
        default:
            $query->orderBy('created_at', 'desc');
            break;
    }

    // 4. Ambil Data Hasil Filter & Sort
    $products = $query->paginate(5)->withQueryString();
    $categories = Category::orderBy('name', 'asc')->get();

    return view('master_data.products', compact('products', 'categories'));
}
    public function store(Request $request)
    {
        $request->validate([
            'category_id'   => ['required', 'exists:categories,id'],
            'code'          => ['required', 'string', 'max:255', 'unique:products,code'],
            'name'          => ['required', 'string', 'max:255'],
            'stock'         => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ], [
            'category_id.required'   => 'Kategori barang wajib dipilih.',
            'category_id.exists'     => 'Kategori barang tidak valid.',
            'code.required'          => 'Kode barang wajib diisi.',
            'code.unique'            => 'Kode barang sudah digunakan.',
            'name.required'          => 'Nama barang wajib diisi.',
            'stock.required'         => 'Stok awal wajib diisi.',
            'stock.integer'          => 'Stok awal harus berupa angka.',
            'stock.min'              => 'Stok awal tidak boleh kurang dari 0.',
            'minimum_stock.required' => 'Stok minimum wajib diisi.',
            'minimum_stock.integer'  => 'Stok minimum harus berupa angka.',
            'minimum_stock.min'      => 'Stok minimum tidak boleh kurang dari 0.',
        ]);

        Product::create([
            'category_id'   => $request->category_id,
            'code'          => $request->code,
            'name'          => $request->name,
            'stock'         => $request->stock,
            'minimum_stock' => $request->minimum_stock,
        ]);

        return redirect()
            ->route('master-data.daftar-barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, Product $daftar_barang)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'code'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'code')->ignore($daftar_barang->id),
            ],
            'name'          => ['required', 'string', 'max:255'],
            'stock'         => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ], [
            'category_id.required'   => 'Kategori barang wajib dipilih.',
            'category_id.exists'     => 'Kategori barang tidak valid.',
            'code.required'          => 'Kode barang wajib diisi.',
            'code.unique'            => 'Kode barang sudah digunakan.',
            'name.required'          => 'Nama barang wajib diisi.',
            'stock.required'         => 'Stok awal wajib diisi.',
            'stock.integer'          => 'Stok awal harus berupa angka.',
            'stock.min'              => 'Stok awal tidak boleh kurang dari 0.',
            'minimum_stock.required' => 'Stok minimum wajib diisi.',
            'minimum_stock.integer'  => 'Stok minimum harus berupa angka.',
            'minimum_stock.min'      => 'Stok minimum tidak boleh kurang dari 0.',
        ]);

        $daftar_barang->update([
            'category_id'   => $request->category_id,
            'code'          => $request->code,
            'name'          => $request->name,
            'stock'         => $request->stock,
            'minimum_stock' => $request->minimum_stock,
        ]);

        return redirect()
            ->route('master-data.daftar-barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Product $daftar_barang)
    {
        if ($daftar_barang->stockTransactions()->exists()) {
            return redirect()
                ->route('master-data.daftar-barang.index')
                ->with(
                    'error',
                    'Barang tidak dapat dihapus karena sudah memiliki riwayat transaksi stok.'
                );
        }

        $daftar_barang->delete();

        return redirect()
            ->route('master-data.daftar-barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}