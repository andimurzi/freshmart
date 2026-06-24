@extends('layouts.admin')

@section('title', 'Kelola Kategori')

@section('content')

<div class="grid gap-6 lg:grid-cols-3">
    {{-- Form tambah kategori (INSERT) --}}
    <div class="animate-fade-up h-fit rounded-3xl border-2 border-ink/10 bg-white p-6">
        <h2 class="font-display text-xl font-semibold">＋ Kategori Baru</h2>

        @if ($errors->any())
            <div class="mt-4 rounded-2xl border-2 border-tomato/30 bg-tomato/10 p-3 text-xs font-semibold text-tomato">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.kategori.store') }}" class="mt-4 space-y-4">
            @csrf
            <label class="block">
                <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Nama kategori</span>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Misal: Bumbu Dapur"
                       class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
            </label>
            <label class="block">
                <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Ikon (emoji)</span>
                <input type="text" name="icon" value="{{ old('icon') }}" required maxlength="10" placeholder="🌶️"
                       class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
            </label>
            <label class="block">
                <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Deskripsi (opsional)</span>
                <textarea name="description" rows="2"
                          class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">{{ old('description') }}</textarea>
            </label>
            <button type="submit" class="w-full rounded-full bg-leaf-700 py-3 font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">Simpan Kategori</button>
        </form>
    </div>

    {{-- Daftar kategori (QUERY) --}}
    <div class="animate-fade-up overflow-hidden rounded-3xl border-2 border-ink/10 bg-white lg:col-span-2" style="animation-delay:.1s">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[520px] text-left text-sm">
                <thead>
                    <tr class="border-b-2 border-ink/10 bg-leaf-50/60 text-xs uppercase tracking-wider text-ink/50">
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Deskripsi</th>
                        <th class="px-5 py-3.5">Produk</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr class="border-b border-ink/5 transition hover:bg-leaf-50/40">
                            <td class="px-5 py-3.5">
                                <span class="mr-2 text-xl">{{ $category->icon }}</span>
                                <span class="font-bold">{{ $category->name }}</span>
                            </td>
                            <td class="max-w-[220px] truncate px-5 py-3.5 text-ink/60">{{ $category->description ?: '—' }}</td>
                            <td class="px-5 py-3.5"><span class="rounded-full bg-leaf-100 px-2.5 py-1 text-xs font-bold text-leaf-800">{{ $category->products_count }}</span></td>
                            <td class="px-5 py-3.5">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.kategori.edit', $category) }}"
                                       class="rounded-full border-2 border-ink/10 bg-white px-3.5 py-1.5 text-xs font-bold transition hover:border-leaf-500 hover:text-leaf-700">✏️ Edit</a>
                                    <form method="POST" action="{{ route('admin.kategori.destroy', $category) }}"
                                          onsubmit="return confirm('Hapus kategori \'{{ $category->name }}\'?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-full border-2 border-ink/10 bg-white px-3.5 py-1.5 text-xs font-bold text-tomato transition hover:border-tomato">🗑️ Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-14 text-center text-sm text-ink/50">Belum ada kategori.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
