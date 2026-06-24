<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * CRUD LENGKAP produk (kriteria penilaian):
 * - Query     : index() menampilkan data dengan pagination
 * - Searching : index() mendukung ?q= untuk mencari nama produk
 * - Insert    : create() + store() dengan upload foto
 * - Update    : edit() + update()
 * - Delete    : destroy()
 */
class ProductController extends Controller
{
    public const UNITS = ['kg', 'gram', 'ikat', 'pcs', 'pack', 'liter', 'sisir'];

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $products = Product::with('category')
            ->when($search, fn ($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'search'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units      = self::UNITS;

        return view('admin.products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, imageRequired: true);

        $data['slug']  = $this->uniqueSlug($data['name']);
        $data['image'] = $this->uploadImage($request);

        Product::create($data);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk "' . $data['name'] . '" berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $units      = self::UNITS;

        return view('admin.products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, imageRequired: false);

        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        if ($request->hasFile('image')) {
            $this->deleteUploadedImage($product);
            $data['image'] = $this->uploadImage($request);
        }

        $product->update($data);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk "' . $product->name . '" berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $this->deleteUploadedImage($product);
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    /* ----------------------- Helpers ----------------------- */

    private function validated(Request $request, bool $imageRequired): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'exists:categories,id'],
            'price'       => ['required', 'integer', 'min:100'],
            'stock'       => ['required', 'integer', 'min:0'],
            'unit'        => ['required', 'in:' . implode(',', self::UNITS)],
            'description' => ['required', 'string'],
            'image'       => [
                $imageRequired ? 'required' : 'nullable',
                'image', 'mimes:jpg,jpeg,png,webp', 'max:2048',
            ],
            'is_featured' => ['nullable', 'boolean'],
            'is_active'   => ['required', 'boolean'],
        ], [
            'name.required'        => 'Nama produk wajib diisi.',
            'category_id.required' => 'Pilih kategori produk.',
            'price.required'       => 'Harga wajib diisi.',
            'price.min'            => 'Harga minimal Rp 100.',
            'stock.required'       => 'Stok wajib diisi.',
            'description.required' => 'Deskripsi wajib diisi.',
            'image.required'       => 'Unggah foto produk.',
            'image.image'          => 'File harus berupa gambar.',
            'image.mimes'          => 'Format foto harus JPG, PNG, atau WEBP.',
            'image.max'            => 'Ukuran foto maksimal 2 MB.',
        ]) + ['is_featured' => $request->boolean('is_featured')];
    }

    private function uploadImage(Request $request): string
    {
        $file     = $request->file('image');
        $filename = uniqid('produk_') . '.' . $file->getClientOriginalExtension();

        // Simpan langsung ke public/uploads agar bisa diakses tanpa storage:link
        $file->move(public_path('uploads'), $filename);

        return 'uploads/' . $filename;
    }

    private function deleteUploadedImage(Product $product): void
    {
        // Hanya hapus file hasil upload, jangan hapus gambar seed bawaan
        if ($product->image && str_starts_with($product->image, 'uploads/')) {
            @unlink(public_path($product->image));
        }
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Product::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
