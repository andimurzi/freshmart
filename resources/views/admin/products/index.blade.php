@extends('layouts.admin')

@section('title', 'Kelola Produk')

@section('content')

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    {{-- Form SEARCHING (kriteria CRUD: Searching) --}}
    <form method="GET" action="{{ route('admin.produk.index') }}" class="flex w-full max-w-sm items-center gap-2">
        <div class="relative flex-1">
            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2">🔍</span>
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama produk…"
                   class="w-full rounded-full border-2 border-ink/10 bg-white py-2.5 pl-11 pr-4 text-sm font-semibold outline-none transition focus:border-leaf-500">
        </div>
        <button type="submit" class="rounded-full bg-ink px-5 py-2.5 text-sm font-bold text-cream transition hover:bg-leaf-800">Cari</button>
    </form>

    <a href="{{ route('admin.produk.create') }}"
       class="rounded-full bg-leaf-700 px-5 py-2.5 text-sm font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">
        ＋ Tambah Produk
    </a>
</div>

@if ($search)
    <p class="mb-4 text-sm text-ink/60">Hasil pencarian untuk "<strong>{{ $search }}</strong>" — {{ $products->total() }} produk.
        <a href="{{ route('admin.produk.index') }}" class="font-bold text-leaf-700 hover:underline">Reset</a>
    </p>
@endif

<div class="animate-fade-up overflow-hidden rounded-3xl border-2 border-ink/10 bg-white">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead>
                <tr class="border-b-2 border-ink/10 bg-leaf-50/60 text-xs uppercase tracking-wider text-ink/50">
                    <th class="px-5 py-3.5">Produk</th>
                    <th class="px-5 py-3.5">Kategori</th>
                    <th class="px-5 py-3.5">Harga</th>
                    <th class="px-5 py-3.5">Stok</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-b border-ink/5 transition hover:bg-leaf-50/40">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->image_url }}" alt="" class="h-12 w-12 rounded-xl border-2 border-ink/10 object-cover">
                                <div>
                                    <p class="font-bold">{{ $product->name }} @if($product->is_featured)<span title="Produk unggulan">⭐</span>@endif</p>
                                    <p class="text-xs text-ink/50">{{ $product->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">{{ $product->category->icon }} {{ $product->category->name }}</td>
                        <td class="px-5 py-3.5 font-bold">{{ $product->price_label }}<span class="text-xs font-medium text-ink/50">/{{ $product->unit }}</span></td>
                        <td class="px-5 py-3.5">
                            <span class="font-bold {{ $product->stock < 10 ? 'text-tomato' : '' }}">{{ $product->stock }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $product->is_active ? 'bg-lime-100 text-leaf-700' : 'bg-stone-200 text-stone-600' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex justify-end gap-2">
                                {{-- UPDATE --}}
                                <a href="{{ route('admin.produk.edit', $product) }}"
                                   class="rounded-full border-2 border-ink/10 bg-white px-3.5 py-1.5 text-xs font-bold transition hover:border-leaf-500 hover:text-leaf-700">✏️ Edit</a>
                                {{-- DELETE --}}
                                <form method="POST" action="{{ route('admin.produk.destroy', $product) }}"
                                      onsubmit="return confirm('Hapus produk \'{{ $product->name }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-full border-2 border-ink/10 bg-white px-3.5 py-1.5 text-xs font-bold text-tomato transition hover:border-tomato">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center text-sm text-ink/50">
                            Tidak ada produk{{ $search ? ' yang cocok dengan pencarian' : '' }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $products->links() }}</div>

@endsection
