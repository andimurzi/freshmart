@extends('layouts.app')

@section('title', 'Masuk — FreshMart')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
    <div class="animate-fade-up grid overflow-hidden rounded-[2rem] border-2 border-ink/10 bg-white shadow-card md:grid-cols-2">

        {{-- Panel brand --}}
        <div class="relative hidden flex-col justify-between overflow-hidden bg-leaf-800 p-10 text-cream md:flex">
            <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-lime-300/20 blur-2xl"></div>
            <div>
                <p class="font-display text-3xl font-semibold">🥬 Fresh<span class="italic text-lime-300">Mart</span></p>
                <p class="mt-4 max-w-xs text-sm text-cream/75">Masuk dan lanjutkan belanja bahan segar favoritmu — keranjangmu menunggu!</p>
            </div>
            <div class="space-y-3 text-sm font-semibold">
                <p class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-full bg-cream/10">🌅</span> Panen pagi, kirim hari ini</p>
                <p class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-full bg-cream/10">🔒</span> Akun & transaksi aman</p>
                <p class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-full bg-cream/10">🎁</span> Promo khusus member</p>
            </div>
            <span class="animate-float absolute bottom-8 right-8 text-6xl">🧺</span>
        </div>

        {{-- Form --}}
        <div class="p-8 sm:p-10">
            <h1 class="font-display text-3xl font-semibold">Selamat datang! 👋</h1>
            <p class="mt-2 text-sm text-ink/60">Masuk ke akun FreshMart-mu.</p>

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-8 space-y-5">
                @csrf

                <label class="block">
                    <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="kamu@email.com"
                           class="w-full rounded-2xl border-2 px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500 {{ $errors->has('email') ? 'border-tomato bg-tomato/5' : 'border-ink/10 bg-cream' }}">
                    @error('email')
                        <span class="mt-1.5 block text-xs font-bold text-tomato">{{ $message }}</span>
                    @enderror
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Kata sandi</span>
                    <div class="relative">
                        <input type="password" name="password" id="login-password" required placeholder="••••••••"
                               class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 pr-12 text-sm font-semibold outline-none transition focus:border-leaf-500">
                        <button type="button" data-toggle-password="login-password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-lg" aria-label="Lihat kata sandi">👁️</button>
                    </div>
                    @error('password')
                        <span class="mt-1.5 block text-xs font-bold text-tomato">{{ $message }}</span>
                    @enderror
                </label>

                <label class="flex cursor-pointer items-center gap-2.5 text-sm font-semibold">
                    <input type="checkbox" name="remember" value="1" class="h-5 w-5 rounded border-2 border-ink/20 accent-leaf-700">
                    Ingat saya di perangkat ini
                </label>

                <button type="submit"
                        class="w-full rounded-full bg-leaf-700 py-3.5 font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">
                    Masuk →
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-ink/60">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-leaf-700 hover:underline">Daftar gratis</a>
            </p>

            <div class="mt-6 rounded-2xl bg-leaf-50 p-4 text-xs text-ink/60">
                <p class="font-bold text-leaf-800">🔑 Akun demo:</p>
                <p class="mt-1">Admin: <code>admin@freshmart.test</code> / <code>password123</code></p>
                <p>Pelanggan: <code>budi@example.com</code> / <code>password123</code></p>
            </div>
        </div>
    </div>
</section>
@endsection
