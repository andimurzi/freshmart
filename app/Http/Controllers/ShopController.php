<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Katalog belanja. Halaman /belanja adalah SPA:
 * pencarian, filter kategori, filter harga, sort, dan pagination
 * semuanya berjalan lewat fetch() ke endpoint api() di bawah
 * TANPA reload halaman (lihat resources/views/shop/index.blade.php).
 */
class ShopController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        $maxPrice   = (int) ceil((Product::active()->max('price') ?? 100000) / 5000) * 5000;

        return view('shop.index', compact('categories', 'maxPrice'));
    }

    /**
     * Endpoint JSON untuk SPA katalog.
     * Mendukung: ?q= (search), ?kategori= (slug), ?harga_maks=, ?sort=, ?page=
     */
    public function api(Request $request)
    {
        $query = Product::active()->with('category');

        // Searching (kriteria CRUD: Searching)
        if ($search = trim((string) $request->query('q'))) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($slug = $request->query('kategori')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        }

        if ($max = (int) $request->query('harga_maks')) {
            $query->where('price', '<=', $max);
        }

        match ($request->query('sort')) {
            'termurah' => $query->orderBy('price'),
            'termahal' => $query->orderByDesc('price'),
            'nama'     => $query->orderBy('name'),
            default    => $query->latest(),
        };

        $products = $query->paginate(9);

        return response()->json([
            'data' => collect($products->items())->map(fn (Product $p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'slug'        => $p->slug,
                'price'       => $p->price,
                'price_label' => $p->price_label,
                'unit'        => $p->unit,
                'stock'       => $p->stock,
                'image_url'   => $p->image_url,
                'url'         => route('shop.show', $p),
                'category'    => [
                    'name' => $p->category->name,
                    'icon' => $p->category->icon,
                ],
            ]),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load('category');

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}
