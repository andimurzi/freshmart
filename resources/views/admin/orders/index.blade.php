@extends('layouts.admin')

@section('title', 'Kelola Pesanan')

@section('content')

@if (session('success'))
    <div class="mb-4 rounded-2xl border-2 border-lime-200 bg-lime-50 px-4 py-3 text-sm font-semibold text-lime-800">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- Filter --}}
<form method="GET" action="{{ route('admin.pesanan.index') }}" class="mb-6 flex flex-wrap items-center gap-2">
    <label class="text-xs font-bold uppercase tracking-wider text-ink/50">Status Pesanan:</label>
    <select name="status" onchange="this.form.submit()"
            class="rounded-full border-2 border-ink/10 bg-white px-4 py-2.5 text-sm font-semibold outline-none transition focus:border-leaf-500">
        <option value="">Semua</option>
        @foreach (\App\Models\Order::STATUSES as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>

    <label class="text-xs font-bold uppercase tracking-wider text-ink/50">Status Bayar:</label>
    <select name="payment_status" onchange="this.form.submit()"
            class="rounded-full border-2 border-ink/10 bg-white px-4 py-2.5 text-sm font-semibold outline-none transition focus:border-leaf-500">
        <option value="">Semua</option>
        @foreach (\App\Models\Order::PAYMENT_STATUSES as $ps)
            <option value="{{ $ps }}" @selected(request('payment_status') === $ps)>{{ ucfirst(str_replace('_', ' ', $ps)) }}</option>
        @endforeach
    </select>

    @if (request('status') || request('payment_status'))
        <a href="{{ route('admin.pesanan.index') }}" class="text-sm font-bold text-leaf-700 hover:underline">Reset</a>
    @endif
</form>

<div class="animate-fade-up space-y-4">
    @forelse ($orders as $order)
        <article class="rounded-3xl border-2 border-ink/10 bg-white p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-mono text-sm font-bold text-leaf-700">{{ $order->invoice_number }}</p>
                    <p class="text-xs text-ink/50">
                        {{ $order->created_at->format('d M Y, H:i') }} ·
                        <strong>{{ $order->name }}</strong> ({{ $order->phone }})
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full px-3 py-1 text-xs font-bold capitalize {{ $order->status_color }}">{{ $order->status }}</span>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $order->payment_status_color }}">{{ $order->payment_status_label }}</span>
                    <p class="font-display text-xl font-semibold text-leaf-700">{{ $order->total_label }}</p>
                </div>
            </div>

            <div class="mt-4 grid gap-4 border-t-2 border-dashed border-ink/10 pt-4 md:grid-cols-2">
                <div class="text-sm space-y-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-ink/50">Pengiriman & Pembayaran</p>
                    <p class="text-ink/75">📍 {{ $order->address }}, {{ $order->city }}</p>
                    <p class="text-ink/75">🗓️ {{ $order->delivery_date->format('d M Y') }} ({{ $order->delivery_time }})</p>
                    <p class="text-ink/75">💳 {{ $order->payment_method_label }} {{ $order->is_gift ? '· 🎁 Hadiah' : '' }}</p>
                    @if ($order->notes)
                        <p class="text-xs italic text-ink/50">📝 "{{ $order->notes }}"</p>
                    @endif

                    {{-- Bukti Pembayaran --}}
                    @if ($order->payment_proof)
                        <div class="mt-2">
                            <p class="text-xs font-bold text-ink/50">Bukti Pembayaran:</p>
                            <a href="{{ Storage::url($order->payment_proof) }}" target="_blank"
                               class="mt-1 inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1.5 text-xs font-bold text-sky-700 hover:bg-sky-100 transition">
                                🖼️ Lihat Bukti Transfer
                            </a>
                        </div>
                    @elseif ($order->needsPaymentProof())
                        <p class="mt-1 text-xs font-semibold text-rose-600">⚠️ Belum ada bukti pembayaran</p>
                    @endif

                    <details class="mt-3">
                        <summary class="cursor-pointer text-xs font-bold text-leaf-700 hover:underline">Lihat {{ $order->items->count() }} item pesanan</summary>
                        <ul class="mt-2 space-y-1 rounded-2xl bg-leaf-50/60 p-3 text-xs">
                            @foreach ($order->items as $item)
                                <li class="flex justify-between gap-3">
                                    <span>{{ $item->qty }} × {{ $item->product_name }}</span>
                                    <span class="font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </details>
                </div>

                {{-- Form update status --}}
                <form method="POST" action="{{ route('admin.pesanan.update', $order) }}"
                      class="space-y-3 md:justify-end">
                    @csrf
                    @method('PATCH')

                    <div class="flex gap-2">
                        <label class="flex-1">
                            <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Status Pesanan</span>
                            <select name="status"
                                    class="w-full rounded-full border-2 border-ink/10 bg-cream px-4 py-2.5 text-sm font-semibold outline-none transition focus:border-leaf-500">
                                @foreach (\App\Models\Order::STATUSES as $status)
                                    <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    {{-- Status pembayaran (khusus metode yang butuh bukti) --}}
                    <div class="flex gap-2">
                        <label class="flex-1">
                            <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Status Pembayaran</span>
                            <select name="payment_status"
                                    class="w-full rounded-full border-2 border-ink/10 bg-cream px-4 py-2.5 text-sm font-semibold outline-none transition focus:border-leaf-500">
                                @foreach (\App\Models\Order::PAYMENT_STATUSES as $ps)
                                    <option value="{{ $ps }}" @selected($order->payment_status === $ps)>{{ ucfirst(str_replace('_', ' ', $ps)) }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <label>
                        <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Catatan Admin (opsional)</span>
                        <input type="text" name="payment_note" value="{{ $order->payment_note }}"
                               placeholder="Misal: bukti tidak valid, harap upload ulang…"
                               class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-2.5 text-sm outline-none transition focus:border-leaf-500">
                    </label>

                    <button type="submit" class="w-full rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-cream transition hover:bg-leaf-800">
                        💾 Simpan Perubahan
                    </button>
                </form>
            </div>
        </article>
    @empty
        <div class="rounded-3xl border-2 border-dashed border-ink/15 bg-white/60 px-6 py-20 text-center">
            <p class="text-5xl">📭</p>
            <p class="mt-3 font-display text-xl font-semibold">Belum ada pesanan{{ request('status') ? ' dengan status "' . request('status') . '"' : '' }}</p>
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $orders->links() }}</div>

@endsection
