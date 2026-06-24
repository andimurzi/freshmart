<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FreshMart — Belanja Segar Setiap Hari')</title>

    {{-- Font: Fraunces (display) + Plus Jakarta Sans (teks) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,400;1,9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS via CDN (tanpa build step — memudahkan deploy) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream:  '#FAF6EE',
                        ink:    '#1C3327',
                        tomato: '#E8553F',
                        sun:    '#F5B82E',
                        leaf: {
                            50: '#F0F7F1', 100: '#DCEEDF', 200: '#BBDDC3',
                            300: '#8FC49E', 400: '#5CA474', 500: '#3A8757',
                            600: '#2A6E45', 700: '#235A3A', 800: '#1E4830', 900: '#193B28',
                        },
                    },
                    fontFamily: {
                        display: ['Fraunces', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'sans-serif'],
                    },
                    boxShadow: {
                        card: '0 12px 30px -14px rgba(25, 59, 40, .28)',
                    },
                },
            },
        };
    </script>

    <link rel="stylesheet" href="{{ asset('css/freshmart.css') }}">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🥬</text></svg>">
</head>

@php
    $cartCount = collect(session('cart', []))->sum();
@endphp

<body class="bg-cream font-sans text-ink antialiased">

    {{-- ===================== NAVBAR ===================== --}}
    <header class="sticky top-0 z-50 border-b-2 border-ink/10 bg-cream/85 backdrop-blur-md">
        <nav class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="group flex items-center gap-2">
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-leaf-700 text-xl shadow-card transition group-hover:rotate-6">🥬</span>
                <span class="font-display text-2xl font-semibold tracking-tight">Fresh<span class="italic text-leaf-600">Mart</span></span>
            </a>

            <div class="hidden items-center gap-1 md:flex">
                <a href="{{ route('home') }}" class="rounded-full px-4 py-2 text-sm font-semibold transition hover:bg-leaf-100 {{ request()->routeIs('home') ? 'bg-leaf-100 text-leaf-800' : '' }}">Beranda</a>
                <a href="{{ route('shop.index') }}" class="rounded-full px-4 py-2 text-sm font-semibold transition hover:bg-leaf-100 {{ request()->routeIs('shop.*') ? 'bg-leaf-100 text-leaf-800' : '' }}">Belanja</a>
                <a href="{{ route('home') }}#video" class="rounded-full px-4 py-2 text-sm font-semibold transition hover:bg-leaf-100">Tentang</a>
                @auth
                    <a href="{{ route('orders.index') }}" class="rounded-full px-4 py-2 text-sm font-semibold transition hover:bg-leaf-100 {{ request()->routeIs('orders.*') ? 'bg-leaf-100 text-leaf-800' : '' }}">Pesanan Saya</a>
                @endauth
            </div>

            <div class="flex items-center gap-2">
                {{-- Keranjang --}}
                <a href="{{ route('cart.index') }}" class="relative grid h-10 w-10 place-items-center rounded-full border-2 border-ink/10 bg-white text-lg transition hover:border-leaf-400 hover:bg-leaf-50" aria-label="Keranjang">
                    🧺
                    <span data-cart-count
                          class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-tomato px-1 text-[11px] font-bold text-white {{ $cartCount < 1 ? 'hidden' : '' }}">{{ $cartCount }}</span>
                </a>

                @guest
                    <a href="{{ route('login') }}" class="hidden rounded-full px-4 py-2 text-sm font-bold text-leaf-700 transition hover:bg-leaf-100 sm:block">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-leaf-700 px-4 py-2 text-sm font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">Daftar</a>
                @endguest

                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hidden rounded-full bg-ink px-4 py-2 text-sm font-bold text-lime-300 transition hover:-translate-y-0.5 sm:block">⚙️ Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border-2 border-ink/10 bg-white px-4 py-2 text-sm font-bold transition hover:border-tomato hover:text-tomato" title="Keluar ({{ auth()->user()->name }})">Keluar</button>
                    </form>
                @endauth

                {{-- Tombol menu mobile --}}
                <button data-menu-toggle class="grid h-10 w-10 place-items-center rounded-full border-2 border-ink/10 bg-white md:hidden" aria-label="Menu">☰</button>
            </div>
        </nav>

        {{-- Menu mobile --}}
        <div id="mobile-menu" class="hidden border-t-2 border-ink/10 bg-cream px-4 py-3 md:hidden">
            <div class="flex flex-col gap-1 text-sm font-semibold">
                <a href="{{ route('home') }}" class="rounded-xl px-3 py-2 hover:bg-leaf-100">Beranda</a>
                <a href="{{ route('shop.index') }}" class="rounded-xl px-3 py-2 hover:bg-leaf-100">Belanja</a>
                <a href="{{ route('home') }}#video" class="rounded-xl px-3 py-2 hover:bg-leaf-100">Tentang</a>
                @auth
                    <a href="{{ route('orders.index') }}" class="rounded-xl px-3 py-2 hover:bg-leaf-100">Pesanan Saya</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-xl px-3 py-2 hover:bg-leaf-100">⚙️ Panel Admin</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="rounded-xl px-3 py-2 hover:bg-leaf-100">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- ===================== KONTEN ===================== --}}
    <main class="min-h-[70vh]">
        @yield('content')
    </main>

    {{-- ===================== FOOTER ===================== --}}
    <footer class="mt-20 border-t-4 border-leaf-700 bg-ink text-cream">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-3">
            <div>
                <p class="font-display text-2xl font-semibold">🥬 Fresh<span class="italic text-lime-300">Mart</span></p>
                <p class="mt-3 max-w-xs text-sm text-cream/70">Pasar segar online — panen pagi, sampai di dapurmu sore ini. Dukung petani lokal Indonesia. 🇮🇩</p>
            </div>
            <div class="text-sm">
                <p class="mb-3 font-bold uppercase tracking-widest text-lime-300">Jelajah</p>
                <div class="flex flex-col gap-2 text-cream/80">
                    <a href="{{ route('shop.index') }}" class="hover:text-lime-300">Semua Produk</a>
                    <a href="{{ route('home') }}#kategori" class="hover:text-lime-300">Kategori</a>
                    <a href="{{ route('home') }}#video" class="hover:text-lime-300">Tentang Kami</a>
                </div>
            </div>
            <div class="text-sm">
                <p class="mb-3 font-bold uppercase tracking-widest text-lime-300">Hubungi</p>
                <p class="text-cream/80">📍 Jl. Pasar Segar No. 17, Bandung<br>📞 0812-3456-7890<br>✉️ halo@freshmart.test</p>
            </div>
        </div>
        <div class="border-t border-cream/10 py-4 text-center text-xs text-cream/50">
            © {{ date('Y') }} FreshMart — Tugas Besar Praktikum Pemrograman Web. Dibuat dengan Laravel.
        </div>
    </footer>

    {{-- Toast untuk flash message dari server --}}
    <div id="toast-stack"></div>

    <script>
        window.FRESHMART = {
            cartAddUrl: @json(route('cart.add')),
        };
    </script>
    <script src="{{ asset('js/freshmart.js') }}"></script>

    @if (session('success'))
        <script>document.addEventListener('DOMContentLoaded', () => toast(@json(session('success'))));</script>
    @endif
    @if (session('error'))
        <script>document.addEventListener('DOMContentLoaded', () => toast(@json(session('error')), 'error'));</script>
    @endif

    @stack('scripts')
</body>
</html>
