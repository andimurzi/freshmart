{{-- Partial form produk — dipakai oleh create.blade.php & edit.blade.php.
     Variabel: $categories, $units, opsional $product --}}

@if ($errors->any())
    <div class="animate-pop mb-6 rounded-2xl border-2 border-tomato/30 bg-tomato/10 p-4 text-sm font-semibold text-tomato">
        <p class="mb-1">⚠️ Mohon periksa kembali:</p>
        <ul class="list-inside list-disc space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-5 lg:col-span-2">
        <div class="rounded-3xl border-2 border-ink/10 bg-white p-6">
            <label class="block">
                <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Nama produk</span>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required placeholder="Misal: Mangga Harum Manis"
                       class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
            </label>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <label>
                    <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Kategori</span>
                    <select name="category_id" required
                            class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
                        <option value="" disabled {{ old('category_id', $product->category_id ?? '') ? '' : 'selected' }}>— Pilih kategori —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id ?? 0) === $category->id)>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Satuan</span>
                    <select name="unit" required
                            class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
                        @foreach ($units as $unit)
                            <option value="{{ $unit }}" @selected(old('unit', $product->unit ?? 'kg') === $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Harga (Rp)</span>
                    <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" min="100" step="100" required placeholder="35000"
                           class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
                </label>

                <label>
                    <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Stok</span>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required
                           class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
                </label>
            </div>

            <label class="mt-4 block">
                <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Deskripsi</span>
                <textarea name="description" rows="4" required placeholder="Ceritakan keunggulan produk ini…"
                          class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">{{ old('description', $product->description ?? '') }}</textarea>
            </label>
        </div>
    </div>

    <div class="space-y-5">
        {{-- Upload foto (kriteria multimedia: foto + input type file) --}}
        <div class="rounded-3xl border-2 border-ink/10 bg-white p-6">
            <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Foto produk</span>

            <img id="image-preview"
                 src="{{ isset($product) ? $product->image_url : '' }}"
                 alt="Pratinjau"
                 class="mb-3 aspect-square w-full rounded-2xl border-2 border-dashed border-ink/15 object-cover {{ isset($product) ? '' : 'hidden' }}">

            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" {{ isset($product) ? '' : 'required' }}
                   onchange="const f=this.files[0]; if(f){const p=document.getElementById('image-preview'); p.src=URL.createObjectURL(f); p.classList.remove('hidden');}"
                   class="block w-full text-xs font-semibold file:mr-3 file:rounded-full file:border-0 file:bg-leaf-700 file:px-4 file:py-2.5 file:text-xs file:font-bold file:text-cream hover:file:bg-leaf-600">
            <p class="mt-2 text-xs text-ink/50">JPG/PNG/WEBP, maks 2 MB.{{ isset($product) ? ' Kosongkan jika tidak diganti.' : '' }}</p>
        </div>

        <div class="rounded-3xl border-2 border-ink/10 bg-white p-6">
            <label class="flex cursor-pointer items-center gap-3 text-sm font-semibold">
                <input type="checkbox" name="is_featured" value="1"
                       @checked(old('is_featured', $product->is_featured ?? false))
                       class="h-5 w-5 rounded border-2 border-ink/20 accent-leaf-700">
                ⭐ Jadikan produk unggulan (tampil di beranda)
            </label>

            <p class="mt-5 mb-1.5 text-xs font-bold uppercase tracking-wider text-ink/50">Status produk</p>
            <div class="grid grid-cols-2 gap-3">
                @foreach ([1 => '✅ Aktif', 0 => '⛔ Nonaktif'] as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="is_active" value="{{ $value }}" class="peer sr-only"
                               @checked((int) old('is_active', $product->is_active ?? 1) === $value)>
                        <span class="block rounded-2xl border-2 border-ink/10 bg-cream px-3 py-2.5 text-center text-xs font-bold transition peer-checked:border-leaf-600 peer-checked:bg-leaf-50 peer-checked:text-leaf-800">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <button type="submit"
                class="w-full rounded-full bg-leaf-700 py-3.5 font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">
            💾 {{ isset($product) ? 'Simpan Perubahan' : 'Simpan Produk' }}
        </button>
        <a href="{{ route('admin.produk.index') }}" class="block text-center text-sm font-bold text-ink/50 hover:text-ink">← Batal, kembali ke daftar</a>
    </div>
</div>
