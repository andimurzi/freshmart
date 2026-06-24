@extends('layouts.app')

@section('title', 'Pesanan Saya — FreshMart')

@section('content')
<section class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <h1 class="animate-fade-up font-display text-3xl font-semibold sm:text-4xl">Pesanan Saya 📦</h1>

    @if ($orders->isEmpty())
        <div class="animate-fade-up mt-10 rounded-3xl border-2 border-dashed border-ink/15 bg-white/60 px-6 py-20 text-center">
            <p class="text-6xl">🍃</p>
            <p class="mt-4 font-display text-2xl font-semibold">Belum ada pesanan</p>
            <p class="mt-2 text-sm text-ink/60">Pesanan pertamamu akan muncul di sini.</p>
            <a href="{{ route('shop.index') }}" class="mt-6 inline-block rounded-full bg-leaf-700 px-7 py-3 font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">Mulai Belanja →</a>
        </div>
    @else
        <div class="mt-8 space-y-5">
            @foreach ($orders as $i => $order)
                <article class="animate-fade-up rounded-3xl border-2 border-ink/10 bg-white p-5 sm:p-6" style="animation-delay: {{ $i * 70 }}ms">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b-2 border-dashed border-ink/10 pb-4">
                        <div>
                            <p class="font-mono text-sm font-bold text-leaf-700">{{ $order->invoice_number }}</p>
                            <p class="text-xs text-ink/50">Dipesan {{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="rounded-full px-3 py-1 text-xs font-bold capitalize {{ $order->status_color }}">{{ $order->status }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $order->payment_status_color }}">{{ $order->payment_status_label }}</span>
                        </div>
                    </div>

                    <ul class="mt-4 space-y-1.5 text-sm">
                        @foreach ($order->items as $item)
                            <li class="flex justify-between gap-3">
                                <span class="text-ink/70">{{ $item->qty }} × {{ $item->product_name }}</span>
                                <span class="font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t-2 border-dashed border-ink/10 pt-4 text-sm">
                        <div class="space-y-1">
                            <p class="text-xs text-ink/50">🚚 {{ $order->city }} · {{ $order->delivery_date->format('d M Y') }} ({{ $order->delivery_time }})</p>
                            <p class="text-xs text-ink/50">{{ $order->payment_method_label }}</p>
                            {{-- Tombol upload bukti jika belum --}}
                            @if ($order->needsPaymentProof() && $order->payment_status === 'unpaid')
                                <a href="{{ route('checkout.success', $order) }}"
                                   class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-800 hover:bg-amber-200 transition">
                                    ⚠️ Upload Bukti Pembayaran
                                </a>
                            @endif
                            @if ($order->payment_note)
                                <p class="text-xs font-semibold text-rose-600">📝 Admin: {{ $order->payment_note }}</p>
                            @endif
                        </div>
                        <p class="font-display text-xl font-semibold text-leaf-700">{{ $order->total_label }}</p>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">{{ $orders->links() }}</div>
    @endif
</section>
@endsection
