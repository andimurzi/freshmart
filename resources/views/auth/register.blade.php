@extends('layouts.app')

@section('title', 'Daftar — FreshMart')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6">
    <div class="animate-fade-up grid overflow-hidden rounded-[2rem] border-2 border-ink/10 bg-white shadow-card md:grid-cols-2">

        {{-- Panel brand --}}
        <div class="relative hidden flex-col justify-between overflow-hidden bg-leaf-800 p-10 text-cream md:flex">
            <div class="pointer-events-none absolute -left-16 -bottom-16 h-56 w-56 rounded-full bg-lime-300/20 blur-2xl"></div>
            <div>
                <p class="font-display text-3xl font-semibold">🥬 Fresh<span class="italic text-lime-300">Mart</span></p>
                <p class="mt-4 max-w-xs text-sm text-cream/75">Buat akun gratis dan rasakan belanja pasar tanpa keluar rumah.</p>
            </div>
            <ul class="space-y-3 text-sm font-semibold">
                <li class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-full bg-cream/10">✅</span> Gratis selamanya</li>
                <li class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-full bg-cream/10">🚚</span> Gratis ongkir min. belanja</li>
                <li class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-full bg-cream/10">📦</span> Lacak semua pesananmu</li>
            </ul>
            <span class="animate-float absolute bottom-8 right-8 text-6xl">🍓</span>
        </div>

        {{-- Form --}}
        <div class="p-8 sm:p-10">
            <h1 class="font-display text-3xl font-semibold">Buat akun baru ✨</h1>
            <p class="mt-2 text-sm text-ink/60">Cuma butuh satu menit, kok.</p>

            <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5">
                @csrf

                <label class="block">
                    <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Nama lengkap</span>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Nama kamu"
                           class="w-full rounded-2xl border-2 px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500 {{ $errors->has('name') ? 'border-tomato bg-tomato/5' : 'border-ink/10 bg-cream' }}">
                    @error('name') <span class="mt-1.5 block text-xs font-bold text-tomato">{{ $message }}</span> @enderror
                </label>

                <div class="grid gap-5 sm:grid-cols-2">
                    <label class="block">
                        <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="kamu@email.com"
                               class="w-full rounded-2xl border-2 px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500 {{ $errors->has('email') ? 'border-tomato bg-tomato/5' : 'border-ink/10 bg-cream' }}">
                        @error('email') <span class="mt-1.5 block text-xs font-bold text-tomato">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">No. HP</span>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx"
                               class="w-full rounded-2xl border-2 px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500 {{ $errors->has('phone') ? 'border-tomato bg-tomato/5' : 'border-ink/10 bg-cream' }}">
                        @error('phone') <span class="mt-1.5 block text-xs font-bold text-tomato">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div>
                    <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Jenis kelamin</span>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach (['L' => '👨 Laki-laki', 'P' => '👩 Perempuan'] as $value => $label)
                            <label class="cursor-pointer">
                                <input type="radio" name="gender" value="{{ $value }}" class="peer sr-only" @checked(old('gender') === $value)>
                                <span class="block rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-center text-sm font-bold transition peer-checked:border-leaf-600 peer-checked:bg-leaf-50 peer-checked:text-leaf-800">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('gender') <span class="mt-1.5 block text-xs font-bold text-tomato">{{ $message }}</span> @enderror
                </div>

                <label class="block">
                    <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Kata sandi</span>
                    <div class="relative">
                        <input type="password" name="password" id="reg-password" required placeholder="Minimal 6 karakter"
                               class="w-full rounded-2xl border-2 px-4 py-3 pr-12 text-sm font-semibold outline-none transition focus:border-leaf-500 {{ $errors->has('password') ? 'border-tomato bg-tomato/5' : 'border-ink/10 bg-cream' }}">
                        <button type="button" data-toggle-password="reg-password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-lg" aria-label="Lihat kata sandi">👁️</button>
                    </div>
                    @error('password') <span class="mt-1.5 block text-xs font-bold text-tomato">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Ulangi kata sandi</span>
                    <input type="password" name="password_confirmation" required placeholder="Ketik ulang kata sandi"
                           class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
                </label>

                <label class="flex cursor-pointer items-start gap-3 text-xs font-semibold text-ink/70">
                    <input type="checkbox" name="terms" value="1" required @checked(old('terms'))
                           class="mt-0.5 h-5 w-5 rounded border-2 border-ink/20 accent-leaf-700">
                    Saya menyetujui syarat & ketentuan serta kebijakan privasi FreshMart.
                </label>
                @error('terms') <span class="-mt-3 block text-xs font-bold text-tomato">{{ $message }}</span> @enderror

                <button type="submit"
                        class="w-full rounded-full bg-leaf-700 py-3.5 font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">
                    Daftar Sekarang 🎉
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-ink/60">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-leaf-700 hover:underline">Masuk</a>
            </p>
        </div>
    </div>
</section>
@endsection
