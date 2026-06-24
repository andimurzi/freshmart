@extends('layouts.app')

@section('title', 'Pesanan Berhasil — FreshMart')

@section('content')
<section class="mx-auto max-w-2xl px-4 py-14 sm:px-6">

    {{-- Animasi centang sukses --}}
    <div class="text-center">
        <svg class="mx-auto h-28 w-28" viewBox="0 0 110 110" fill="none">
            <circle class="success-circle" cx="55" cy="55" r="50" stroke="#2A6E45" stroke-width="6" stroke-linecap="round"/>
            <path class="success-check" d="M34 57 L49 71 L78 41" stroke="#2A6E45" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        <h1 class="animate-fade-up mt-6 font-display text-3xl font-semibold sm:text-4xl" style="animation-delay:.3s">Pesanan diterima! 🎉</h1>
        <p class="animate-fade-up mt-3 text-ink/60" style="animation-delay:.4s">
            Terima kasih, {{ $order->name }}! Tim FreshMart sedang menyiapkan belanjaanmu.
        </p>
    </div>

    {{-- Ringkasan Pesanan --}}
    <div class="animate-fade-up mx-auto mt-8 rounded-3xl border-2 border-ink/10 bg-white p-6 shadow-card" style="animation-delay:.5s">
        <div class="flex items-center justify-between border-b-2 border-dashed border-ink/10 pb-4">
            <span class="text-xs font-bold uppercase tracking-widest text-ink/50">No. Invoice</span>
            <span class="rounded-full bg-leaf-50 px-3 py-1 font-mono text-sm font-bold text-leaf-700">{{ $order->invoice_number }}</span>
        </div>

        <ul class="mt-4 space-y-2 text-sm">
            @foreach ($order->items as $item)
                <li class="flex justify-between gap-3">
                    <span class="text-ink/70">{{ $item->qty }} × {{ $item->product_name }}</span>
                    <span class="font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </li>
            @endforeach
        </ul>

        <div class="my-4 border-t-2 border-dashed border-ink/10"></div>
        <div class="flex justify-between text-sm"><span class="text-ink/60">Kirim ke</span><span class="font-bold">{{ $order->city }}, {{ $order->delivery_date->format('d M Y') }} ({{ $order->delivery_time }})</span></div>
        <div class="mt-1 flex justify-between text-sm">
            <span class="text-ink/60">Pembayaran</span>
            <span class="font-bold">{{ $order->payment_method_label }}</span>
        </div>
        <div class="mt-1 flex justify-between text-sm">
            <span class="text-ink/60">Status Bayar</span>
            <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $order->payment_status_color }}">{{ $order->payment_status_label }}</span>
        </div>
        <div class="mt-3 flex items-baseline justify-between">
            <span class="font-bold">Total</span>
            <span class="font-display text-2xl font-semibold text-leaf-700">{{ $order->total_label }}</span>
        </div>
    </div>

    {{-- ===== SECTION KHUSUS TRANSFER MANUAL ===== --}}
    @if ($order->payment_method === 'manual')
        <div class="animate-fade-up mt-6 rounded-3xl border-2 border-amber-200 bg-amber-50 p-6" style="animation-delay:.55s">
            <h2 class="font-display text-xl font-semibold text-amber-900">🧾 Instruksi Transfer Manual</h2>
            <p class="mt-2 text-sm text-amber-800">Transfer tepat sebesar <strong class="text-amber-900">{{ $order->total_label }}</strong> ke salah satu rekening berikut:</p>

            <div class="mt-4 space-y-2">
                @foreach ($bankAccounts as $acct)
                    <div class="rounded-2xl border border-amber-200 bg-white p-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-amber-800">Bank {{ $acct['bank'] }}</span>
                            <button onclick="navigator.clipboard.writeText('{{ $acct['no'] }}')" class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700 hover:bg-amber-200 transition">📋 Salin</button>
                        </div>
                        <p class="mt-1 font-mono text-lg font-bold text-leaf-700">{{ $acct['no'] }}</p>
                        <p class="text-xs text-ink/50">a/n {{ $acct['name'] }}</p>
                    </div>
                @endforeach
            </div>

            <p class="mt-4 rounded-xl bg-amber-100 px-4 py-2.5 text-xs font-semibold text-amber-800">
                ⚠️ Gunakan nomor invoice <strong>{{ $order->invoice_number }}</strong> sebagai keterangan transfer.
            </p>

            {{-- Form upload bukti --}}
            @if (session('success'))
                <div class="mt-4 rounded-2xl bg-lime-100 px-4 py-3 text-sm font-semibold text-lime-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($order->payment_status !== 'paid')
                <form method="POST" action="{{ route('checkout.upload-proof', $order) }}"
                      enctype="multipart/form-data" class="mt-5">
                    @csrf
                    <label>
                        <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-amber-700">Upload Bukti Transfer</span>
                        <input type="file" name="payment_proof" accept="image/*" required
                               class="w-full rounded-2xl border-2 border-amber-200 bg-white px-4 py-3 text-sm file:mr-3 file:rounded-full file:border-0 file:bg-amber-100 file:px-4 file:py-1.5 file:text-xs file:font-bold file:text-amber-800">
                    </label>
                    @error('payment_proof')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                    <button type="submit"
                            class="mt-3 w-full rounded-full bg-amber-600 py-3 font-bold text-white transition hover:bg-amber-700">
                        Kirim Bukti Pembayaran 📤
                    </button>
                </form>
            @else
                <div class="mt-4 rounded-2xl bg-lime-100 px-4 py-3 text-sm font-bold text-lime-800">✅ Bukti pembayaran sudah diterima & diverifikasi.</div>
            @endif

            @if ($order->payment_proof && $order->payment_status === 'waiting_verification')
                <p class="mt-3 text-center text-xs text-amber-700">🔍 Bukti sudah diterima, menunggu verifikasi admin (maks. 1×24 jam).</p>
            @endif
        </div>
    @endif

    {{-- ===== SECTION KHUSUS QRIS ===== --}}
    @if ($order->payment_method === 'qris')
        <div class="animate-fade-up mt-6 rounded-3xl border-2 border-sky-200 bg-sky-50 p-6" style="animation-delay:.55s">
            <h2 class="font-display text-xl font-semibold text-sky-900">⚡ Bayar via QRIS</h2>
            <p class="mt-2 text-sm text-sky-800">Scan QR Code di bawah menggunakan GoPay, OVO, DANA, ShopeePay, BCA, atau aplikasi lainnya.</p>

            {{-- QRIS Code --}}
            <div class="mt-5 flex flex-col items-center">
                <div class="rounded-3xl border-4 border-sky-200 bg-white p-4 shadow-card">
                    {{-- Ganti src dengan path QRIS nyata Anda di public/images/qris.png --}}
                    <img src="{{ $qrisImage }}" alt="QRIS FreshMart" class="h-52 w-52 object-contain">
                </div>
                <p class="mt-3 text-sm font-bold text-sky-800">{{ $qrisMerchant }}</p>
                <p class="mt-1 rounded-full bg-sky-100 px-4 py-1.5 font-mono text-base font-bold text-sky-900">{{ $order->total_label }}</p>
                <p class="mt-2 text-xs text-sky-600">Nomor Invoice: <strong>{{ $order->invoice_number }}</strong></p>
            </div>

            <div class="mt-5 rounded-2xl bg-sky-100 p-4 text-xs font-semibold text-sky-800 space-y-1.5">
                <p>1️⃣ Buka aplikasi pembayaran favoritmu</p>
                <p>2️⃣ Pilih menu <strong>Scan QR / QRIS</strong></p>
                <p>3️⃣ Pastikan nominal sesuai: <strong>{{ $order->total_label }}</strong></p>
                <p>4️⃣ Setelah berhasil, screenshot bukti & upload di bawah</p>
            </div>

            {{-- Form upload bukti QRIS --}}
            @if (session('success'))
                <div class="mt-4 rounded-2xl bg-lime-100 px-4 py-3 text-sm font-semibold text-lime-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($order->payment_status !== 'paid')
                <form method="POST" action="{{ route('checkout.upload-proof', $order) }}"
                      enctype="multipart/form-data" class="mt-5">
                    @csrf
                    <label>
                        <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-sky-700">Upload Screenshot Bukti Pembayaran QRIS</span>
                        <input type="file" name="payment_proof" accept="image/*" required
                               class="w-full rounded-2xl border-2 border-sky-200 bg-white px-4 py-3 text-sm file:mr-3 file:rounded-full file:border-0 file:bg-sky-100 file:px-4 file:py-1.5 file:text-xs file:font-bold file:text-sky-800">
                    </label>
                    @error('payment_proof')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                    <button type="submit"
                            class="mt-3 w-full rounded-full bg-sky-600 py-3 font-bold text-white transition hover:bg-sky-700">
                        Kirim Bukti Pembayaran 📤
                    </button>
                </form>
            @else
                <div class="mt-4 rounded-2xl bg-lime-100 px-4 py-3 text-sm font-bold text-lime-800">✅ Bukti pembayaran sudah diterima & diverifikasi.</div>
            @endif

            @if ($order->payment_proof && $order->payment_status === 'waiting_verification')
                <p class="mt-3 text-center text-xs text-sky-700">🔍 Bukti sudah diterima, menunggu verifikasi admin (maks. 1×24 jam).</p>
            @endif
        </div>
    @endif

    {{-- Tombol navigasi --}}
    <div class="animate-fade-up mt-8 flex flex-wrap justify-center gap-3" style="animation-delay:.6s">
        <a href="{{ route('orders.index') }}" class="rounded-full bg-leaf-700 px-7 py-3 font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">Lihat Pesanan Saya</a>
        <a href="{{ route('shop.index') }}" class="rounded-full border-2 border-ink/15 bg-white px-7 py-3 font-bold transition hover:border-leaf-400">Belanja Lagi</a>
    </div>
</section>
@endsection
