@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

{{-- Kartu statistik --}}
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @php
        $cards = [
            ['icon' => '🥕', 'label' => 'Total Produk',   'value' => $stats['products'],   'bg' => 'bg-leaf-100'],
            ['icon' => '🗂️', 'label' => 'Kategori',       'value' => $stats['categories'], 'bg' => 'bg-lime-100'],
            ['icon' => '📦', 'label' => 'Total Pesanan',  'value' => $stats['orders'],     'bg' => 'bg-sun/20'],
            ['icon' => '💰', 'label' => 'Pendapatan',     'value' => 'Rp ' . number_format($stats['revenue'], 0, ',', '.'), 'bg' => 'bg-tomato/10'],
        ];
    @endphp
    @foreach ($cards as $i => $card)
        <div class="animate-fade-up rounded-3xl border-2 border-ink/10 bg-white p-5" style="animation-delay: {{ $i * 70 }}ms">
            <span class="grid h-12 w-12 place-items-center rounded-2xl text-2xl {{ $card['bg'] }}">{{ $card['icon'] }}</span>
            <p class="mt-3 text-xs font-bold uppercase tracking-widest text-ink/50">{{ $card['label'] }}</p>
            <p class="mt-1 font-display text-2xl font-semibold">{{ $card['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="mt-8 grid gap-6 xl:grid-cols-3">
    {{-- Pesanan terbaru --}}
    <div class="animate-fade-up rounded-3xl border-2 border-ink/10 bg-white p-6 xl:col-span-2" style="animation-delay:.2s">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-display text-xl font-semibold">Pesanan Terbaru</h2>
            <a href="{{ route('admin.pesanan.index') }}" class="text-sm font-bold text-leaf-700 hover:underline">Kelola semua →</a>
        </div>

        @if ($recentOrders->isEmpty())
            <p class="py-10 text-center text-sm text-ink/50">Belum ada pesanan masuk.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b-2 border-ink/10 text-xs uppercase tracking-wider text-ink/50">
                            <th class="pb-3 pr-3">Invoice</th>
                            <th class="pb-3 pr-3">Pelanggan</th>
                            <th class="pb-3 pr-3">Total</th>
                            <th class="pb-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr class="border-b border-ink/5">
                                <td class="py-3 pr-3 font-mono text-xs font-bold text-leaf-700">{{ $order->invoice_number }}</td>
                                <td class="py-3 pr-3 font-semibold">{{ $order->name }}</td>
                                <td class="py-3 pr-3 font-bold">{{ $order->total_label }}</td>
                                <td class="py-3"><span class="rounded-full px-2.5 py-1 text-xs font-bold capitalize {{ $order->status_color }}">{{ $order->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Stok menipis --}}
    <div class="animate-fade-up rounded-3xl border-2 border-ink/10 bg-white p-6" style="animation-delay:.28s">
        <h2 class="font-display text-xl font-semibold">⚠️ Stok Menipis</h2>
        @if ($lowStock->isEmpty())
            <p class="py-10 text-center text-sm text-ink/50">Semua stok aman 👍</p>
        @else
            <ul class="mt-4 space-y-3">
                @foreach ($lowStock as $product)
                    <li class="flex items-center gap-3 text-sm">
                        <img src="{{ $product->image_url }}" alt="" class="h-11 w-11 rounded-xl border-2 border-ink/10 object-cover">
                        <span class="min-w-0 flex-1">
                            <a href="{{ route('admin.produk.edit', $product) }}" class="block truncate font-bold hover:text-leaf-700">{{ $product->name }}</a>
                            <span class="text-xs text-ink/50">{{ $product->price_label }}/{{ $product->unit }}</span>
                        </span>
                        <span class="rounded-full bg-tomato/10 px-2.5 py-1 text-xs font-bold text-tomato">{{ $product->stock }} {{ $product->unit }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

@endsection
