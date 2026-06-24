@extends('layouts.app')

@section('title', 'Keranjang — FreshMart')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
    <h1 class="animate-fade-up font-display text-3xl font-semibold sm:text-4xl">Keranjang Belanja 🧺</h1>

    @if ($items->isEmpty())
        <div class="animate-fade-up mt-10 rounded-3xl border-2 border-dashed border-ink/15 bg-white/60 px-6 py-20 text-center">
            <p class="text-6xl">🛒</p>
            <p class="mt-4 font-display text-2xl font-semibold">Keranjangmu masih kosong</p>
            <p class="mt-2 text-sm text-ink/60">Yuk isi dengan bahan segar pilihan!</p>
            <a href="{{ route('shop.index') }}" class="mt-6 inline-block rounded-full bg-leaf-700 px-7 py-3 font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">Mulai Belanja →</a>
        </div>
    @else
        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            {{-- Daftar item --}}
            <div class="space-y-4 lg:col-span-2">
                @foreach ($items as $i => $item)
                    <div data-cart-row
                         class="animate-fade-up flex flex-wrap items-center gap-4 rounded-3xl border-2 border-ink/10 bg-white p-4"
                         style="animation-delay: {{ $i * 70 }}ms">
                        <a href="{{ route('shop.show', $item->product) }}" class="shrink-0">
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                 class="h-20 w-20 rounded-2xl border-2 border-ink/10 object-cover">
                        </a>

                        <div class="min-w-0 flex-1">
                            <a href="{{ route('shop.show', $item->product) }}" class="block truncate font-bold hover:text-leaf-700">{{ $item->product->name }}</a>
                            <p class="text-xs text-ink/50">{{ $item->product->category->icon }} {{ $item->product->category->name }} · {{ $item->product->price_label }}/{{ $item->product->unit }}</p>

                            <div class="mt-2 flex items-center gap-3">
                                <div class="flex items-center overflow-hidden rounded-full border-2 border-ink/10">
                                    <button type="button" data-cart-step="-1" class="px-3 py-1.5 font-bold transition hover:bg-leaf-50">−</button>
                                    <input type="number" data-cart-qty="{{ route('cart.update', $item->product) }}"
                                           value="{{ $item->qty }}" min="1" max="{{ max($item->product->stock, 1) }}"
                                           class="w-14 border-x-2 border-ink/10 bg-transparent py-1.5 text-center text-sm font-bold outline-none">
                                    <button type="button" data-cart-step="1" class="px-3 py-1.5 font-bold transition hover:bg-leaf-50">＋</button>
                                </div>
                                <button type="button" data-cart-remove="{{ route('cart.remove', $item->product) }}"
                                        class="text-xs font-bold text-tomato hover:underline">Hapus</button>
                            </div>
                        </div>

                        <p data-cart-subtotal class="ml-auto font-display text-lg font-semibold text-leaf-700">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Ringkasan --}}
            <aside class="animate-fade-up h-fit rounded-3xl border-2 border-ink/10 bg-white p-6 shadow-card lg:sticky lg:top-24" style="animation-delay:.15s">
                <h2 class="font-display text-xl font-semibold">Ringkasan</h2>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-ink/60">Subtotal</dt><dd data-cart-total class="font-bold">Rp {{ number_format($total, 0, ',', '.') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-ink/60">Ongkir</dt><dd class="font-bold text-leaf-700">Gratis 🎉</dd></div>
                </dl>
                <div class="my-4 border-t-2 border-dashed border-ink/10"></div>
                <div class="flex items-baseline justify-between">
                    <p class="font-bold">Total</p>
                    <p data-cart-total class="font-display text-2xl font-semibold text-leaf-700">Rp {{ number_format($total, 0, ',', '.') }}</p>
                </div>
                <a href="{{ auth()->check() ? route('checkout.index') : route('login') }}"
                   class="mt-6 block rounded-full bg-leaf-700 py-3.5 text-center font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">
                    Lanjut ke Checkout →
                </a>
                @guest
                    <p class="mt-3 text-center text-xs text-ink/50">Kamu perlu <a href="{{ route('login') }}" class="font-bold text-leaf-700 hover:underline">masuk</a> dulu untuk checkout.</p>
                @endguest
            </aside>
        </div>
    @endif
</section>
@endsection
