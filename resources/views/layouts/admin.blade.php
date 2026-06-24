<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') — FreshMart</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

<body class="bg-cream font-sans text-ink antialiased">
<div class="flex min-h-screen">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside id="admin-sidebar"
           class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col bg-ink text-cream transition-transform duration-300 lg:static lg:translate-x-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 border-b border-cream/10 px-5 py-5">
            <span class="grid h-10 w-10 place-items-center rounded-2xl bg-leaf-700 text-xl">🥬</span>
            <span>
                <span class="block font-display text-lg font-semibold leading-tight">Fresh<span class="italic text-lime-300">Mart</span></span>
                <span class="block text-[11px] uppercase tracking-widest text-cream/50">Panel Admin</span>
            </span>
        </a>

        @php
            $menu = [
                ['route' => 'admin.dashboard',     'is' => 'admin.dashboard',  'icon' => '📊', 'label' => 'Dashboard'],
                ['route' => 'admin.produk.index',  'is' => 'admin.produk.*',   'icon' => '🥕', 'label' => 'Produk'],
                ['route' => 'admin.kategori.index','is' => 'admin.kategori.*', 'icon' => '🗂️', 'label' => 'Kategori'],
                ['route' => 'admin.pesanan.index', 'is' => 'admin.pesanan.*',  'icon' => '📦', 'label' => 'Pesanan'],
            ];
        @endphp

        <nav class="flex-1 space-y-1 px-3 py-4">
            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold transition
                          {{ request()->routeIs($item['is']) ? 'bg-leaf-700 text-lime-200' : 'text-cream/75 hover:bg-cream/10 hover:text-cream' }}">
                    <span>{{ $item['icon'] }}</span> {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="space-y-1 border-t border-cream/10 px-3 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-semibold text-cream/75 transition hover:bg-cream/10 hover:text-cream">🏪 Lihat Toko</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-left text-sm font-semibold text-cream/75 transition hover:bg-tomato/20 hover:text-tomato">🚪 Keluar</button>
            </form>
        </div>
    </aside>

    {{-- ===================== KONTEN ===================== --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-30 flex items-center justify-between gap-3 border-b-2 border-ink/10 bg-cream/85 px-4 py-3 backdrop-blur-md sm:px-6">
            <div class="flex items-center gap-3">
                <button data-sidebar-toggle class="grid h-10 w-10 place-items-center rounded-full border-2 border-ink/10 bg-white lg:hidden" aria-label="Menu">☰</button>
                <h1 class="font-display text-xl font-semibold sm:text-2xl">@yield('title', 'Panel Admin')</h1>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <span class="hidden text-ink/60 sm:block">Halo, <strong>{{ auth()->user()->name }}</strong> 👋</span>
                <span class="grid h-9 w-9 place-items-center rounded-full bg-leaf-700 font-bold text-cream">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6">
            @yield('content')
        </main>
    </div>
</div>

<div id="toast-stack"></div>

<script>
    window.FRESHMART = { cartAddUrl: @json(route('cart.add')) };
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
