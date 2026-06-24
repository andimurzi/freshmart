@extends('layouts.app')

@section('title', $product->name . ' — FreshMart')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">

    {{-- Breadcrumb --}}
    <nav class="animate-fade-up text-xs font-semibold text-ink/50">
        <a href="{{ route('home') }}" class="hover:text-leaf-700">Beranda</a>
        <span class="mx-1.5">/</span>
        <a href="{{ route('shop.index', ['kategori' => $product->category->slug]) }}" class="hover:text-leaf-700">{{ $product->category->name }}</a>
        <span class="mx-1.5">/</span>
        <span class="text-ink">{{ $product->name }}</span>
    </nav>

    <div class="mt-6 grid gap-10 md:grid-cols-2">
        {{-- Foto produk --}}
        <div class="animate-fade-up">
            <div class="card-lift overflow-hidden rounded-3xl border-2 border-ink/10 bg-white shadow-card">
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="card-img aspect-square w-full object-cover">
            </div>
        </div>

        {{-- Info produk --}}
        <div class="animate-fade-up" style="animation-delay:.1s">
            <span class="inline-flex items-center gap-1.5 rounded-full border-2 border-leaf-300 bg-leaf-50 px-3 py-1 text-xs font-bold text-leaf-700">
                {{ $product->category->icon }} {{ $product->category->name }}
            </span>

            <h1 class="mt-4 font-display text-3xl font-semibold leading-tight sm:text-4xl">{{ $product->name }}</h1>

            <p class="mt-4 font-display text-3xl font-semibold text-leaf-700">
                {{ $product->price_label }}
                <span class="font-sans text-sm font-medium text-ink/50">/ {{ $product->unit }}</span>
            </p>

            <p class="mt-2 inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold
                {{ $product->stock < 10 ? 'bg-tomato/10 text-tomato' : 'bg-lime-100 text-leaf-700' }}">
                {{ $product->stock < 10 ? '⚠️ Stok tinggal ' . $product->stock : '✅ Stok tersedia: ' . $product->stock }} {{ $product->unit }}
            </p>

            <p class="mt-5 leading-relaxed text-ink/70">{{ $product->description }}</p>

            {{-- Stepper jumlah + tombol keranjang --}}
            <div class="mt-8 flex flex-wrap items-center gap-3">
                <div class="flex items-center overflow-hidden rounded-full border-2 border-ink/10 bg-white">
                    <button type="button" class="px-4 py-3 text-lg font-bold transition hover:bg-leaf-50"
                            onclick="const i=document.querySelector('[data-qty-input]'); i.value=Math.max(1, parseInt(i.value||1)-1)">−</button>
                    <input type="number" data-qty-input value="1" min="1" max="{{ max($product->stock, 1) }}"
                           class="w-16 border-x-2 border-ink/10 bg-transparent py-3 text-center text-sm font-bold outline-none">
                    <button type="button" class="px-4 py-3 text-lg font-bold transition hover:bg-leaf-50"
                            onclick="const i=document.querySelector('[data-qty-input]'); i.value=Math.min({{ max($product->stock, 1) }}, parseInt(i.value||1)+1)">＋</button>
                </div>

                <button type="button" data-add-to-cart="{{ $product->id }}"
                        class="rounded-full bg-leaf-700 px-7 py-3.5 font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">
                    🧺 Tambah ke Keranjang
                </button>
            </div>

            <div class="mt-8 grid grid-cols-3 gap-3 text-center text-xs font-semibold text-ink/60">
                <div class="rounded-2xl border-2 border-ink/10 bg-white p-3">🚚<br>Kirim hari ini</div>
                <div class="rounded-2xl border-2 border-ink/10 bg-white p-3">❄️<br>Boks dingin</div>
                <div class="rounded-2xl border-2 border-ink/10 bg-white p-3">↩️<br>Garansi segar</div>
            </div>
        </div>
    </div>

    {{-- Produk terkait --}}
    @if ($related->isNotEmpty())
        <div class="mt-16">
            <h2 class="reveal font-display text-2xl font-semibold">Sekalian belanja ini? 🛒</h2>
            <div class="mt-6 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($related as $i => $rel)
                    <article class="reveal card-lift flex flex-col overflow-hidden rounded-3xl border-2 border-ink/10 bg-white" style="--reveal-delay: {{ $i * 80 }}ms">
                        <a href="{{ route('shop.show', $rel) }}" class="block overflow-hidden">
                            <img src="{{ $rel->image_url }}" alt="{{ $rel->name }}" class="card-img aspect-square w-full object-cover" loading="lazy">
                        </a>
                        <div class="flex flex-1 flex-col p-4">
                            <a href="{{ route('shop.show', $rel) }}" class="text-sm font-bold leading-snug hover:text-leaf-700">{{ $rel->name }}</a>
                            <div class="mt-auto flex items-center justify-between gap-2 pt-3">
                                <p class="font-display font-semibold text-leaf-700">{{ $rel->price_label }}</p>
                                <button type="button" data-add-to-cart="{{ $rel->id }}"
                                        class="grid h-9 w-9 place-items-center rounded-full bg-leaf-700 text-cream transition hover:bg-leaf-600"
                                        aria-label="Tambah {{ $rel->name }} ke keranjang">＋</button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</section>
@endsection
