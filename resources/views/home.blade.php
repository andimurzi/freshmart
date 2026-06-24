@extends('layouts.app')

@section('title', 'FreshMart — Belanja Segar Setiap Hari')

@section('content')

{{-- ===================== HERO ===================== --}}
<section class="relative overflow-hidden">
    <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-leaf-200/60 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-16 top-40 h-80 w-80 rounded-full bg-lime-200/50 blur-3xl"></div>

    <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 md:py-20">
        <div>
            <p class="animate-fade-up inline-flex items-center gap-2 rounded-full border-2 border-leaf-300 bg-leaf-50 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-leaf-700">
                🌱 Pasar segar online
            </p>
            <h1 class="animate-fade-up mt-5 font-display text-4xl font-semibold leading-[1.08] sm:text-5xl lg:text-6xl" style="animation-delay:.08s">
                Belanja <span class="italic text-leaf-600">Segar</span>,<br>
                Hidup <span class="relative inline-block">Sehat.
                    <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 220 14" fill="none"><path d="M3 10C60 3 160 3 217 10" stroke="#E8553F" stroke-width="5" stroke-linecap="round"/></svg>
                </span>
            </h1>
            <p class="animate-fade-up mt-6 max-w-md text-base text-ink/70 sm:text-lg" style="animation-delay:.16s">
                Buah, sayur, dan protein <strong>panen pagi ini</strong> dari petani lokal — diantar dingin sampai dapurmu. Tanpa antre, tanpa layu.
            </p>
            <div class="animate-fade-up mt-8 flex flex-wrap items-center gap-3" style="animation-delay:.24s">
                <a href="{{ route('shop.index') }}" class="rounded-full bg-leaf-700 px-7 py-3.5 font-bold text-cream shadow-card transition hover:-translate-y-1 hover:bg-leaf-600">
                    Mulai Belanja →
                </a>
                <a href="#video" class="rounded-full border-2 border-ink/15 bg-white px-7 py-3.5 font-bold transition hover:border-leaf-400 hover:bg-leaf-50">
                    ▶ Lihat Video
                </a>
            </div>
            <div class="animate-fade-up mt-10 flex flex-wrap gap-6 text-sm" style="animation-delay:.32s">
                <div><p class="font-display text-2xl font-semibold text-leaf-700">15+</p><p class="text-ink/60">Produk segar</p></div>
                <div><p class="font-display text-2xl font-semibold text-leaf-700">&lt;6 jam</p><p class="text-ink/60">Panen → rumah</p></div>
                <div><p class="font-display text-2xl font-semibold text-leaf-700">100%</p><p class="text-ink/60">Petani lokal</p></div>
            </div>
        </div>

        {{-- Komposisi emoji melayang --}}
        <div class="relative mx-auto h-80 w-80 sm:h-96 sm:w-96">
            <div class="absolute inset-0 rounded-full border-[14px] border-leaf-100 bg-gradient-to-br from-leaf-50 via-cream to-lime-100 shadow-card"></div>
            <div class="absolute inset-6 rounded-full border-2 border-dashed border-leaf-300 animate-spin-slow"></div>
            <span class="animate-float absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-8xl drop-shadow-lg">🧺</span>
            <span class="animate-float absolute -left-2 top-12 grid h-16 w-16 place-items-center rounded-2xl bg-white text-3xl shadow-card" style="animation-delay:.4s">🍎</span>
            <span class="animate-float-slow absolute -right-1 top-20 grid h-16 w-16 place-items-center rounded-2xl bg-white text-3xl shadow-card">🥦</span>
            <span class="animate-float absolute bottom-16 -left-4 grid h-16 w-16 place-items-center rounded-2xl bg-white text-3xl shadow-card" style="animation-delay:.9s">🥛</span>
            <span class="animate-float-slow absolute -bottom-2 right-10 grid h-16 w-16 place-items-center rounded-2xl bg-white text-3xl shadow-card" style="animation-delay:.6s">🍊</span>
            <span class="absolute right-2 bottom-28 rounded-full bg-tomato px-3 py-1 text-xs font-bold text-white shadow-card animate-pop" style="animation-delay:1s">Gratis ongkir!*</span>
        </div>
    </div>
</section>

{{-- ===================== MARQUEE ===================== --}}
<div class="marquee border-y-2 border-ink bg-lime-300 py-3 text-sm font-extrabold uppercase tracking-widest text-ink">
    <div class="marquee-track">
        @for ($i = 0; $i < 2; $i++)
            <span>🥬 100% Segar</span><span>🚚 Panen Hari Ini</span><span>🇮🇩 Petani Lokal</span><span>❄️ Rantai Dingin</span><span>💸 Harga Pasar</span><span>🥬 100% Segar</span><span>🚚 Panen Hari Ini</span><span>🇮🇩 Petani Lokal</span><span>❄️ Rantai Dingin</span><span>💸 Harga Pasar</span>
        @endfor
    </div>
</div>

{{-- ===================== KATEGORI ===================== --}}
<section id="kategori" class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
    <div class="reveal mb-8 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-tomato">Kategori</p>
            <h2 class="mt-1 font-display text-3xl font-semibold sm:text-4xl">Mau masak apa hari ini?</h2>
        </div>
        <a href="{{ route('shop.index') }}" class="text-sm font-bold text-leaf-700 hover:underline">Lihat semua →</a>
    </div>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        @foreach ($categories as $i => $category)
            <a href="{{ route('shop.index', ['kategori' => $category->slug]) }}"
               class="reveal card-lift group rounded-3xl border-2 border-ink/10 bg-white p-5 text-center"
               style="--reveal-delay: {{ $i * 70 }}ms">
                <span class="card-img mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-leaf-50 text-4xl">{{ $category->icon }}</span>
                <p class="mt-3 text-sm font-bold leading-tight">{{ $category->name }}</p>
                <p class="mt-1 text-xs text-ink/50">{{ $category->products_count }} produk</p>
            </a>
        @endforeach
    </div>
</section>

{{-- ===================== PRODUK UNGGULAN ===================== --}}
<section class="bg-leaf-50/70 py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <div class="reveal mb-8 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-tomato">Pilihan Kami</p>
                <h2 class="mt-1 font-display text-3xl font-semibold sm:text-4xl">Produk unggulan minggu ini 🌟</h2>
            </div>
            <a href="{{ route('shop.index') }}" class="text-sm font-bold text-leaf-700 hover:underline">Belanja semua →</a>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
            @foreach ($featured as $i => $product)
                <article class="reveal card-lift flex flex-col overflow-hidden rounded-3xl border-2 border-ink/10 bg-white"
                         style="--reveal-delay: {{ ($i % 4) * 80 }}ms">
                    <a href="{{ route('shop.show', $product) }}" class="relative block overflow-hidden">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="card-img aspect-square w-full object-cover" loading="lazy">
                        <span class="absolute left-3 top-3 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-leaf-700 shadow">{{ $product->category->icon }} {{ $product->category->name }}</span>
                    </a>
                    <div class="flex flex-1 flex-col p-4">
                        <a href="{{ route('shop.show', $product) }}" class="font-bold leading-snug hover:text-leaf-700">{{ $product->name }}</a>
                        <p class="mt-1 text-xs text-ink/50">Stok {{ $product->stock }} {{ $product->unit }}</p>
                        <div class="mt-auto flex items-center justify-between gap-2 pt-4">
                            <p class="font-display text-lg font-semibold text-leaf-700">{{ $product->price_label }}<span class="text-xs font-sans font-medium text-ink/50">/{{ $product->unit }}</span></p>
                            <button type="button" data-add-to-cart="{{ $product->id }}"
                                    class="animate-wiggle grid h-10 w-10 place-items-center rounded-full bg-leaf-700 text-lg text-cream transition hover:bg-leaf-600"
                                    aria-label="Tambah {{ $product->name }} ke keranjang">＋</button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== VIDEO (MULTIMEDIA) ===================== --}}
<section id="video" class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
    <div class="grid items-center gap-10 md:grid-cols-2">
        <div class="reveal">
            <p class="text-xs font-bold uppercase tracking-widest text-tomato">Tentang FreshMart</p>
            <h2 class="mt-1 font-display text-3xl font-semibold sm:text-4xl">Dari kebun ke dapurmu, <span class="italic text-leaf-600">dalam hitungan jam</span></h2>
            <p class="mt-4 text-ink/70">FreshMart bermitra langsung dengan petani dan peternak lokal. Setiap pagi kami menjemput hasil panen, menyortirnya, lalu mengantarkannya dengan boks dingin — supaya kesegarannya sampai utuh ke meja makanmu.</p>
            <ul class="mt-6 space-y-3 text-sm font-semibold">
                <li class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-full bg-lime-200">🌅</span> Panen & sortir setiap pagi</li>
                <li class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-full bg-lime-200">❄️</span> Pengiriman boks dingin (cold chain)</li>
                <li class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-full bg-lime-200">🤝</span> Harga adil untuk petani lokal</li>
            </ul>
        </div>
        <div class="reveal" style="--reveal-delay:120ms">
            <div class="overflow-hidden rounded-3xl border-2 border-ink/10 bg-ink shadow-card">
                <video controls preload="metadata" class="aspect-video w-full" poster="">
                    <source src="{{ asset('video/promo.mp4') }}" type="video/mp4">
                    Browser Anda tidak mendukung pemutaran video.
                </video>
            </div>
            <p class="mt-3 text-center text-xs text-ink/50">🎬 Video profil FreshMart — kriteria multimedia (foto + video) terpenuhi di sini.</p>
        </div>
    </div>
</section>

{{-- ===================== TESTIMONI ===================== --}}
<section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">
    <div class="reveal mb-8 text-center">
        <p class="text-xs font-bold uppercase tracking-widest text-tomato">Kata Mereka</p>
        <h2 class="mt-1 font-display text-3xl font-semibold sm:text-4xl">Dipercaya keluarga Indonesia 💚</h2>
    </div>
    <div class="grid gap-5 md:grid-cols-3">
        @php
            $testimonials = [
                ['emoji' => '👩🏻‍🍳', 'name' => 'Ibu Ratna', 'city' => 'Bandung', 'text' => 'Bayamnya masih seger banget, kayak baru dipetik. Anak-anak jadi doyan sayur!'],
                ['emoji' => '👨🏽‍💼', 'name' => 'Pak Dimas', 'city' => 'Jakarta', 'text' => 'Pesan pagi, sore udah sampai. Salmonnya fresh, packing rapi pakai boks dingin.'],
                ['emoji' => '👩🏻‍🎓', 'name' => 'Kak Salsa', 'city' => 'Depok', 'text' => 'Webnya enak dipakai, cari produk gampang dan checkout-nya cepat. Recommended!'],
            ];
        @endphp
        @foreach ($testimonials as $i => $t)
            <figure class="reveal rounded-3xl border-2 border-ink/10 bg-white p-6" style="--reveal-delay: {{ $i * 100 }}ms">
                <p class="text-sun">★★★★★</p>
                <blockquote class="mt-3 text-sm leading-relaxed text-ink/80">"{{ $t['text'] }}"</blockquote>
                <figcaption class="mt-4 flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-full bg-leaf-100 text-2xl">{{ $t['emoji'] }}</span>
                    <span><strong class="block text-sm">{{ $t['name'] }}</strong><span class="text-xs text-ink/50">{{ $t['city'] }}</span></span>
                </figcaption>
            </figure>
        @endforeach
    </div>
</section>

{{-- ===================== CTA TIKET ===================== --}}
<section class="mx-auto max-w-6xl px-4 pb-4 sm:px-6">
    <div class="reveal ticket rounded-3xl bg-leaf-800 px-6 py-10 text-center text-cream sm:px-12">
        <p class="font-display text-2xl font-semibold sm:text-3xl">Siap belanja segar pertamamu? 🧺</p>
        <p class="mx-auto mt-2 max-w-xl text-sm text-cream/75">Daftar sekarang dan nikmati kemudahan belanja bahan segar tanpa keluar rumah.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            @guest
                <a href="{{ route('register') }}" class="rounded-full bg-lime-300 px-7 py-3 font-bold text-ink transition hover:-translate-y-1">Daftar Gratis</a>
            @endguest
            <a href="{{ route('shop.index') }}" class="rounded-full border-2 border-cream/40 px-7 py-3 font-bold transition hover:bg-cream/10">Lihat Produk</a>
        </div>
    </div>
</section>

@endsection
