<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Keranjang belanja berbasis session.
 * Struktur session('cart') = [ product_id => qty ].
 * Tambah/ubah/hapus dipanggil via fetch() (AJAX) dari frontend
 * sehingga keranjang terasa seperti SPA (tanpa reload).
 */
class CartController extends Controller
{
    public function index()
    {
        [$items, $total] = $this->buildCart();

        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'qty'        => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::active()->findOrFail($data['product_id']);
        $qty     = $data['qty'] ?? 1;

        $cart    = session('cart', []);
        $current = $cart[$product->id] ?? 0;
        $newQty  = min($current + $qty, $product->stock); // jangan melebihi stok

        if ($newQty < 1) {
            return response()->json(['message' => 'Stok produk habis.'], 422);
        }

        $cart[$product->id] = $newQty;
        session(['cart' => $cart]);

        return response()->json([
            'message' => $product->name . ' masuk keranjang 🧺',
            'count'   => array_sum($cart),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session('cart', []);

        if (! isset($cart[$product->id])) {
            return response()->json(['message' => 'Produk tidak ada di keranjang.'], 404);
        }

        $cart[$product->id] = min($data['qty'], max($product->stock, 1));
        session(['cart' => $cart]);

        [, $total] = $this->buildCart();
        $subtotal  = $cart[$product->id] * $product->price;

        return response()->json([
            'qty'            => $cart[$product->id],
            'subtotal_label' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'total_label'    => 'Rp ' . number_format($total, 0, ',', '.'),
            'count'          => array_sum($cart),
        ]);
    }

    public function remove(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        [, $total] = $this->buildCart();

        return response()->json([
            'message'     => $product->name . ' dihapus dari keranjang.',
            'total_label' => 'Rp ' . number_format($total, 0, ',', '.'),
            'count'       => array_sum($cart),
            'empty'       => count($cart) === 0,
        ]);
    }

    /**
     * Helper: bangun daftar item keranjang + total dari session.
     *
     * @return array{0: \Illuminate\Support\Collection, 1: int}
     */
    private function buildCart(): array
    {
        $cart = session('cart', []);

        $products = Product::with('category')
            ->whereIn('id', array_keys($cart))
            ->get();

        $items = $products->map(fn (Product $p) => (object) [
            'product'  => $p,
            'qty'      => $cart[$p->id],
            'subtotal' => $cart[$p->id] * $p->price,
        ]);

        return [$items, (int) $items->sum('subtotal')];
    }
}
