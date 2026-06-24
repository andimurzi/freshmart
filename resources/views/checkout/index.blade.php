@extends('layouts.app')

@section('title', 'Checkout — FreshMart')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
    <h1 class="animate-fade-up font-display text-3xl font-semibold sm:text-4xl">Checkout 📦</h1>
    <p class="animate-fade-up mt-2 text-sm text-ink/60">Lengkapi data pengiriman & pembayaran di bawah ini.</p>

    @if ($errors->any())
        <div class="animate-pop mt-6 rounded-2xl border-2 border-tomato/30 bg-tomato/10 p-4 text-sm font-semibold text-tomato">
            <p class="mb-1">⚠️ Mohon periksa kembali:</p>
            <ul class="list-inside list-disc space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 grid gap-8 lg:grid-cols-3"
          enctype="multipart/form-data">
        @csrf

        <div class="space-y-6 lg:col-span-2">
            {{-- ===== Data penerima ===== --}}
            <fieldset class="animate-fade-up rounded-3xl border-2 border-ink/10 bg-white p-6">
                <legend class="sr-only">Data penerima</legend>
                <h2 class="font-display text-xl font-semibold">1 · Data Penerima</h2>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <label>
                        <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Nama lengkap</span>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                               class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
                    </label>
                    <label>
                        <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">No. HP</span>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" placeholder="08xxxxxxxxxx" required
                               class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
                    </label>
                    <label class="sm:col-span-2">
                        <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Email</span>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                               class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
                    </label>
                </div>
            </fieldset>

            {{-- ===== Pengiriman ===== --}}
            <fieldset class="animate-fade-up rounded-3xl border-2 border-ink/10 bg-white p-6" style="animation-delay:.08s">
                <legend class="sr-only">Pengiriman</legend>
                <h2 class="font-display text-xl font-semibold">2 · Alamat & Jadwal Kirim</h2>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <label class="sm:col-span-2">
                        <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Alamat lengkap</span>
                        <textarea name="address" rows="3" required placeholder="Nama jalan, nomor rumah, RT/RW, patokan…"
                                  class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">{{ old('address', auth()->user()->address) }}</textarea>
                    </label>

                    <label>
                        <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Kota</span>
                        <select name="city" required
                                class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
                            <option value="" disabled {{ old('city') ? '' : 'selected' }}>— Pilih kota —</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city }}" @selected(old('city') === $city)>{{ $city }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Tanggal pengiriman</span>
                        <input type="date" name="delivery_date" value="{{ old('delivery_date', now()->toDateString()) }}"
                               min="{{ now()->toDateString() }}" required
                               class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
                    </label>
                </div>

                <p class="mt-5 mb-1.5 text-xs font-bold uppercase tracking-wider text-ink/50">Waktu pengiriman</p>
                <div class="grid grid-cols-3 gap-3">
                    @foreach (['pagi' => '🌅 Pagi (06–10)', 'siang' => '☀️ Siang (10–15)', 'sore' => '🌇 Sore (15–19)'] as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="delivery_time" value="{{ $value }}" class="peer sr-only"
                                   @checked(old('delivery_time', 'pagi') === $value)>
                            <span class="block rounded-2xl border-2 border-ink/10 bg-cream px-3 py-3 text-center text-xs font-bold transition peer-checked:border-leaf-600 peer-checked:bg-leaf-50 peer-checked:text-leaf-800">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>

            {{-- ===== Pembayaran ===== --}}
            <fieldset class="animate-fade-up rounded-3xl border-2 border-ink/10 bg-white p-6" style="animation-delay:.16s">
                <legend class="sr-only">Pembayaran</legend>
                <h2 class="font-display text-xl font-semibold">3 · Metode Pembayaran</h2>

                <p class="mt-4 mb-3 text-xs font-bold uppercase tracking-wider text-ink/50">Pilih metode pembayaran</p>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- COD --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="cod" class="peer sr-only"
                               @checked(old('payment_method', 'cod') === 'cod')>
                        <span class="flex h-full flex-col rounded-2xl border-2 border-ink/10 bg-cream p-4 transition peer-checked:border-leaf-600 peer-checked:bg-leaf-50">
                            <span class="text-2xl">💵</span>
                            <span class="mt-1 block text-sm font-bold">COD</span>
                            <span class="block text-xs text-ink/50">Bayar saat barang tiba</span>
                        </span>
                    </label>

                    {{-- Transfer Bank (lama) --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="transfer" class="peer sr-only"
                               @checked(old('payment_method') === 'transfer')>
                        <span class="flex h-full flex-col rounded-2xl border-2 border-ink/10 bg-cream p-4 transition peer-checked:border-leaf-600 peer-checked:bg-leaf-50">
                            <span class="text-2xl">🏦</span>
                            <span class="mt-1 block text-sm font-bold">Transfer Bank</span>
                            <span class="block text-xs text-ink/50">BCA / Mandiri / BRI</span>
                        </span>
                    </label>

                    {{-- E-Wallet --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="ewallet" class="peer sr-only"
                               @checked(old('payment_method') === 'ewallet')>
                        <span class="flex h-full flex-col rounded-2xl border-2 border-ink/10 bg-cream p-4 transition peer-checked:border-leaf-600 peer-checked:bg-leaf-50">
                            <span class="text-2xl">📱</span>
                            <span class="mt-1 block text-sm font-bold">E-Wallet</span>
                            <span class="block text-xs text-ink/50">GoPay / OVO / DANA</span>
                        </span>
                    </label>

                    {{-- Transfer Manual (BARU) --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="manual" class="peer sr-only"
                               @checked(old('payment_method') === 'manual')>
                        <span class="relative flex h-full flex-col rounded-2xl border-2 border-ink/10 bg-cream p-4 transition peer-checked:border-leaf-600 peer-checked:bg-leaf-50">
                            <span class="absolute right-3 top-2.5 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">MANUAL</span>
                            <span class="text-2xl">🧾</span>
                            <span class="mt-1 block text-sm font-bold">Transfer Manual</span>
                            <span class="block text-xs text-ink/50">Upload bukti transfer</span>
                        </span>
                    </label>

                    {{-- QRIS (BARU) --}}
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="qris" class="peer sr-only"
                               @checked(old('payment_method') === 'qris')>
                        <span class="relative flex h-full flex-col rounded-2xl border-2 border-ink/10 bg-cream p-4 transition peer-checked:border-leaf-600 peer-checked:bg-leaf-50">
                            <span class="absolute right-3 top-2.5 rounded-full bg-sky-100 px-2 py-0.5 text-[10px] font-bold text-sky-700">SCAN QR</span>
                            <span class="text-2xl">⚡</span>
                            <span class="mt-1 block text-sm font-bold">QRIS</span>
                            <span class="block text-xs text-ink/50">Scan & bayar instan</span>
                        </span>
                    </label>
                </div>

                {{-- Info rekening Transfer Manual (tampil saat dipilih) --}}
                <div id="info-manual" class="mt-4 hidden rounded-2xl border-2 border-amber-200 bg-amber-50 p-4 text-sm">
                    <p class="mb-2 font-bold text-amber-800">📋 Info Rekening Transfer Manual</p>
                    <p class="mb-3 text-xs text-amber-700">Setelah memesan, transfer ke salah satu rekening berikut dan upload bukti di halaman konfirmasi.</p>
                    <div class="space-y-2">
                        @foreach ($bankAccounts as $acct)
                            <div class="flex items-center justify-between rounded-xl bg-white px-4 py-2.5 font-mono text-xs font-bold">
                                <span class="text-ink/60">{{ $acct['bank'] }}</span>
                                <span class="text-leaf-700">{{ $acct['no'] }}</span>
                                <span class="text-ink/40">a/n {{ $acct['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Info QRIS (tampil saat dipilih) --}}
                <div id="info-qris" class="mt-4 hidden rounded-2xl border-2 border-sky-200 bg-sky-50 p-4 text-sm">
                    <p class="mb-1 font-bold text-sky-800">⚡ Bayar via QRIS</p>
                    <p class="text-xs text-sky-700">Setelah memesan, QR Code akan ditampilkan di halaman konfirmasi. Scan menggunakan aplikasi apapun (GoPay, OVO, DANA, BCA, dll), lalu upload screenshot bukti pembayaran.</p>
                </div>

                <label class="mt-5 block">
                    <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Catatan untuk kurir (opsional)</span>
                    <textarea name="notes" rows="2" placeholder="Misal: titip ke satpam, jangan pencet bel…"
                              class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">{{ old('notes') }}</textarea>
                </label>

                <label class="mt-4 flex cursor-pointer items-center gap-3 text-sm font-semibold">
                    <input type="checkbox" name="is_gift" value="1" @checked(old('is_gift'))
                           class="h-5 w-5 rounded border-2 border-ink/20 accent-leaf-700">
                    🎁 Kirim sebagai hadiah (sembunyikan harga di paket)
                </label>
            </fieldset>
        </div>

        {{-- ===== Ringkasan pesanan ===== --}}
        <aside class="animate-fade-up h-fit rounded-3xl border-2 border-ink/10 bg-white p-6 shadow-card lg:sticky lg:top-24" style="animation-delay:.2s">
            <h2 class="font-display text-xl font-semibold">Pesananmu</h2>
            <ul class="mt-4 space-y-3">
                @foreach ($items as $item)
                    <li class="flex items-center gap-3 text-sm">
                        <img src="{{ $item->product->image_url }}" alt="" class="h-12 w-12 rounded-xl border-2 border-ink/10 object-cover">
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-bold">{{ $item->product->name }}</span>
                            <span class="text-xs text-ink/50">{{ $item->qty }} × {{ $item->product->price_label }}</span>
                        </span>
                        <span class="font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="my-4 border-t-2 border-dashed border-ink/10"></div>
            <div class="flex items-baseline justify-between">
                <p class="font-bold">Total</p>
                <p class="font-display text-2xl font-semibold text-leaf-700">Rp {{ number_format($total, 0, ',', '.') }}</p>
            </div>

            <label class="mt-5 flex cursor-pointer items-start gap-3 text-xs font-semibold text-ink/70">
                <input type="checkbox" name="agree" value="1" required @checked(old('agree'))
                       class="mt-0.5 h-5 w-5 rounded border-2 border-ink/20 accent-leaf-700">
                Saya menyetujui syarat & ketentuan serta kebijakan pengembalian FreshMart.
            </label>

            <button type="submit"
                    class="mt-5 w-full rounded-full bg-leaf-700 py-3.5 font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">
                Buat Pesanan 🎉
            </button>
        </aside>
    </form>
</section>

<script>
// Tampilkan/sembunyikan info metode pembayaran
document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.getElementById('info-manual').classList.add('hidden');
        document.getElementById('info-qris').classList.add('hidden');
        if (this.value === 'manual') {
            document.getElementById('info-manual').classList.remove('hidden');
        } else if (this.value === 'qris') {
            document.getElementById('info-qris').classList.remove('hidden');
        }
    });
});
// Trigger saat halaman load (jika old value)
var selected = document.querySelector('input[name="payment_method"]:checked');
if (selected) selected.dispatchEvent(new Event('change'));
</script>
@endsection
