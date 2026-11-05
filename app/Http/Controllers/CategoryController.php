<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
{
    $sort = $request->sort ?? 'latest';

    $categories = Category::query();

    $categories->when($request->search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });

    switch ($sort) {

        case 'oldest':
            $categories->orderBy('created_at', 'asc');
            break;

        case 'az':
            $categories->orderBy('name', 'asc');
            break;

        case 'za':
            $categories->orderBy('name', 'desc');
            break;

        default:
            $categories->orderBy('created_at', 'desc');
            break;
    }


    $categories = $categories->paginate(5)->withQueryString();

    return view(
        'master_data.categories',
        compact('categories', 'sort')
    );
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('master-data.kategori-barang.index')
            ->with('success', 'Kategori barang berhasil ditambahkan.');
    }

    public function update(Request $request, Category $kategori_barang)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->ignore($kategori_barang->id),
            ],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
        ]);

        $kategori_barang->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('master-data.kategori-barang.index')
            ->with('success', 'Kategori barang berhasil diperbarui.');
    }

    public function destroy(Category $kategori_barang)
    {
        if ($kategori_barang->products()->exists()) {
            return redirect()
                ->route('master-data.kategori-barang.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data produk.');
        }

        $kategori_barang->delete();

        return redirect()
            ->route('master-data.kategori-barang.index')
            ->with('success', 'Kategori barang berhasil dihapus.');
    }
}