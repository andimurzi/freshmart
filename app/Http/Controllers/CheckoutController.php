<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /** Daftar kota layanan */
    public const CITIES = [
        'Jakarta', 'Bandung', 'Bogor', 'Bekasi', 'Depok',
        'Tangerang', 'Surabaya', 'Yogyakarta', 'Semarang', 'Medan',
    ];

    /** Info rekening untuk Transfer Manual */
    public const BANK_ACCOUNTS = [
        ['bank' => 'BCA',     'no' => '1234567890', 'name' => 'PT FreshMart Indonesia'],
        ['bank' => 'Mandiri', 'no' => '0987654321', 'name' => 'PT FreshMart Indonesia'],
        ['bank' => 'BRI',     'no' => '1122334455', 'name' => 'PT FreshMart Indonesia'],
    ];

    /** QRIS — ganti dengan path gambar QRIS nyata di public/images/qris.png */
    public const QRIS_IMAGE = '/images/qris-placeholder.svg';
    public const QRIS_MERCHANT = 'FreshMart Official';

    public function index(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')
                ->with('error', 'Keranjang masih kosong. Yuk pilih produk dulu!');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();

        $items = $products->map(fn (Product $p) => (object) [
            'product'  => $p,
            'qty'      => $cart[$p->id],
            'subtotal' => $cart[$p->id] * $p->price,
        ]);

        $total        = (int) $items->sum('subtotal');
        $cities       = self::CITIES;
        $bankAccounts = self::BANK_ACCOUNTS;

        return view('checkout.index', compact('items', 'total', 'cities', 'bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'phone'          => ['required', 'string', 'max:25'],
            'email'          => ['required', 'email'],
            'address'        => ['required', 'string', 'max:500'],
            'city'           => ['required', 'in:' . implode(',', self::CITIES)],
            'delivery_date'  => ['required', 'date', 'after_or_equal:today'],
            'delivery_time'  => ['required', 'in:pagi,siang,sore'],
            'payment_method' => ['required', 'in:cod,transfer,ewallet,manual,qris'],
            'notes'          => ['nullable', 'string', 'max:500'],
            'is_gift'        => ['nullable', 'boolean'],
            'agree'          => ['accepted'],
        ], [
            'name.required'           => 'Nama penerima wajib diisi.',
            'phone.required'          => 'Nomor HP wajib diisi.',
            'email.required'          => 'Email wajib diisi.',
            'email.email'             => 'Format email tidak valid.',
            'address.required'        => 'Alamat lengkap wajib diisi.',
            'city.required'           => 'Pilih kota tujuan.',
            'city.in'                 => 'Kota belum termasuk area layanan kami.',
            'delivery_date.required'  => 'Pilih tanggal pengiriman.',
            'delivery_date.after_or_equal' => 'Tanggal pengiriman minimal hari ini.',
            'delivery_time.required'  => 'Pilih waktu pengiriman.',
            'payment_method.required' => 'Pilih metode pembayaran.',
            'agree.accepted'          => 'Centang persetujuan syarat & ketentuan dulu ya.',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')
                ->with('error', 'Keranjang masih kosong.');
        }

        // Metode yang butuh bukti pembayaran → payment_status = unpaid (upload belakangan)
        $needsProof     = in_array($validated['payment_method'], Order::PAYMENT_NEEDS_PROOF);
        $paymentStatus  = 'unpaid';

        // COD, transfer lama, ewallet langsung pending (tidak upload bukti)
        if (! $needsProof) {
            $paymentStatus = 'unpaid';
        }

        $order = DB::transaction(function () use ($validated, $cart, $request, $paymentStatus) {
            $products = Product::whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $total = 0;
            foreach ($cart as $productId => $qty) {
                $product = $products->get($productId);

                if (! $product || $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'cart' => 'Stok "' . ($product->name ?? 'produk') . '" tidak mencukupi.',
                    ]);
                }

                $total += $product->price * $qty;
            }

            $order = Order::create([
                'user_id'        => $request->user()->id,
                'invoice_number' => 'FM-' . now()->format('ymd') . '-' . strtoupper(Str::random(5)),
                'name'           => $validated['name'],
                'phone'          => $validated['phone'],
                'email'          => $validated['email'],
                'address'        => $validated['address'],
                'city'           => $validated['city'],
                'delivery_date'  => $validated['delivery_date'],
                'delivery_time'  => $validated['delivery_time'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paymentStatus,
                'notes'          => $validated['notes'] ?? null,
                'is_gift'        => $request->boolean('is_gift'),
                'total'          => $total,
                'status'         => 'pending',
            ]);

            foreach ($cart as $productId => $qty) {
                $product = $products->get($productId);

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'price'        => $product->price,
                    'qty'          => $qty,
                    'subtotal'     => $product->price * $qty,
                ]);

                $product->decrement('stock', $qty);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('checkout.success', $order);
    }

    public function success(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items');
        $bankAccounts = self::BANK_ACCOUNTS;
        $qrisImage    = self::QRIS_IMAGE;
        $qrisMerchant = self::QRIS_MERCHANT;

        return view('checkout.success', compact('order', 'bankAccounts', 'qrisImage', 'qrisMerchant'));
    }

    /**
     * Upload bukti pembayaran (Transfer Manual / QRIS).
     * Route: POST /checkout/bukti-bayar/{order}
     */
    public function uploadProof(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        // Hanya boleh upload jika belum diverifikasi
        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Pembayaran sudah lunas, tidak perlu upload ulang.');
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ], [
            'payment_proof.required' => 'Pilih file bukti pembayaran.',
            'payment_proof.image'    => 'File harus berupa gambar (JPG/PNG/WEBP).',
            'payment_proof.max'      => 'Ukuran file maksimal 3 MB.',
        ]);

        // Hapus file lama jika ada
        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('payment_proof')
            ->store('payment-proofs', 'public');

        $order->update([
            'payment_proof'  => $path,
            'payment_status' => 'waiting_verification',
        ]);

        return back()->with('success', '✅ Bukti pembayaran berhasil dikirim! Admin akan memverifikasi dalam 1×24 jam.');
    }
}
