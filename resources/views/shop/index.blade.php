@extends('layouts.app')

@section('title', 'Belanja — FreshMart')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6">

    <div class="animate-fade-up">
        <p class="text-xs font-bold uppercase tracking-widest text-tomato">Katalog</p>
        <h1 class="mt-1 font-display text-3xl font-semibold sm:text-4xl">Belanja Segar 🧺</h1>
        <p class="mt-2 max-w-xl text-sm text-ink/60">
            Halaman ini adalah <strong>SPA (Single Page Application)</strong>: pencarian, filter, urutan,
            dan pagination di bawah ini berjalan lewat <code class="rounded bg-leaf-100 px-1">fetch()</code>
            ke API JSON <em>tanpa reload halaman</em>.
        </p>
    </div>

    {{-- ===================== KONTROL FILTER ===================== --}}
    <div class="animate-fade-up mt-8 rounded-3xl border-2 border-ink/10 bg-white p-5 shadow-card" style="animation-delay:.1s">
        <div class="grid gap-4 md:grid-cols-12">
            {{-- Pencarian (input type="search") --}}
            <label class="md:col-span-5">
                <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Cari produk</span>
                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2">🔍</span>
                    <input type="search" id="filter-q" placeholder="Misal: bayam, salmon, apel…"
                           class="w-full rounded-2xl border-2 border-ink/10 bg-cream py-3 pl-11 pr-4 text-sm font-semibold outline-none transition focus:border-leaf-500">
                </div>
            </label>

            {{-- Slider harga maksimum (input type="range") --}}
            <label class="md:col-span-4">
                <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">
                    Harga maksimum: <output id="harga-output" class="text-leaf-700">Rp {{ number_format($maxPrice, 0, ',', '.') }}</output>
                </span>
                <input type="range" id="filter-harga" min="5000" max="{{ $maxPrice }}" step="5000" value="{{ $maxPrice }}"
                       class="mt-3 w-full">
            </label>

            {{-- Urutkan (select) --}}
            <label class="md:col-span-3">
                <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Urutkan</span>
                <select id="filter-sort"
                        class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
                    <option value="terbaru">Terbaru</option>
                    <option value="termurah">Harga terendah</option>
                    <option value="termahal">Harga tertinggi</option>
                    <option value="nama">Nama A–Z</option>
                </select>
            </label>
        </div>

        {{-- Chip kategori --}}
        <div id="chip-kategori" class="mt-4 flex flex-wrap gap-2">
            <button type="button" data-kategori=""
                    class="chip rounded-full border-2 border-ink/10 bg-cream px-4 py-1.5 text-xs font-bold transition hover:border-leaf-400">
                🧺 Semua
            </button>
            @foreach ($categories as $category)
                <button type="button" data-kategori="{{ $category->slug }}"
                        class="chip rounded-full border-2 border-ink/10 bg-cream px-4 py-1.5 text-xs font-bold transition hover:border-leaf-400">
                    {{ $category->icon }} {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ===================== INFO + GRID ===================== --}}
    <p id="hasil-info" class="mt-8 text-sm font-semibold text-ink/50">Memuat produk…</p>

    <div id="product-grid" class="mt-4 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-3"></div>

    {{-- Empty state --}}
    <div id="empty-state" class="hidden rounded-3xl border-2 border-dashed border-ink/15 bg-white/60 px-6 py-16 text-center">
        <p class="text-5xl">🥲</p>
        <p class="mt-3 font-display text-xl font-semibold">Produk tidak ditemukan</p>
        <p class="mt-1 text-sm text-ink/60">Coba ubah kata kunci atau hapus filter ya.</p>
    </div>

    {{-- Pagination --}}
    <div id="pagination" class="mt-10 flex flex-wrap items-center justify-center gap-2"></div>
</section>
@endsection

@push('scripts')
<script>
(() => {
    const API_URL = @json(route('shop.api'));

    const grid     = document.getElementById('product-grid');
    const info     = document.getElementById('hasil-info');
    const empty    = document.getElementById('empty-state');
    const pagiWrap = document.getElementById('pagination');
    const inputQ   = document.getElementById('filter-q');
    const inputHrg = document.getElementById('filter-harga');
    const outHrg   = document.getElementById('harga-output');
    const selSort  = document.getElementById('filter-sort');
    const chips    = [...document.querySelectorAll('#chip-kategori .chip')];

    const fmt = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
    const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[c]));

    // ---------- State SPA (dibaca dari URL agar bisa dibagikan/back-forward) ----------
    const params = new URLSearchParams(location.search);
    const state = {
        q: params.get('q') || '',
        kategori: params.get('kategori') || '',
        harga_maks: params.get('harga_maks') || inputHrg.max,
        sort: params.get('sort') || 'terbaru',
        page: parseInt(params.get('page') || '1', 10),
    };

    function syncControls() {
        inputQ.value = state.q;
        inputHrg.value = state.harga_maks;
        outHrg.textContent = fmt(inputHrg.value);
        selSort.value = state.sort;
        chips.forEach((chip) => {
            const active = chip.dataset.kategori === state.kategori;
            chip.classList.toggle('bg-leaf-700', active);
            chip.classList.toggle('text-cream', active);
            chip.classList.toggle('border-leaf-700', active);
            chip.classList.toggle('bg-cream', !active);
        });
    }

    function pushUrl() {
        const p = new URLSearchParams();
        if (state.q) p.set('q', state.q);
        if (state.kategori) p.set('kategori', state.kategori);
        if (state.harga_maks && state.harga_maks !== inputHrg.max) p.set('harga_maks', state.harga_maks);
        if (state.sort !== 'terbaru') p.set('sort', state.sort);
        if (state.page > 1) p.set('page', state.page);
        const qs = p.toString();
        history.pushState(null, '', location.pathname + (qs ? '?' + qs : ''));
    }

    function skeleton() {
        grid.innerHTML = Array.from({ length: 6 }, () => `
            <div class="overflow-hidden rounded-3xl border-2 border-ink/10 bg-white">
                <div class="skeleton aspect-square w-full"></div>
                <div class="space-y-2 p-4">
                    <div class="skeleton h-4 w-3/4 rounded-full"></div>
                    <div class="skeleton h-4 w-1/2 rounded-full"></div>
                </div>
            </div>`).join('');
        empty.classList.add('hidden');
        info.textContent = 'Memuat produk…';
    }

    function cardHtml(p, i) {
        return `
        <article class="card-lift animate-fade-up flex flex-col overflow-hidden rounded-3xl border-2 border-ink/10 bg-white" style="animation-delay:${(i % 9) * 60}ms">
            <a href="${p.url}" class="relative block overflow-hidden">
                <img src="${p.image_url}" alt="${esc(p.name)}" class="card-img aspect-square w-full object-cover" loading="lazy">
                <span class="absolute left-3 top-3 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-leaf-700 shadow">${p.category.icon} ${esc(p.category.name)}</span>
                ${p.stock < 10 ? '<span class="absolute right-3 top-3 rounded-full bg-tomato px-3 py-1 text-xs font-bold text-white shadow">Stok tipis!</span>' : ''}
            </a>
            <div class="flex flex-1 flex-col p-4">
                <a href="${p.url}" class="font-bold leading-snug hover:text-leaf-700">${esc(p.name)}</a>
                <p class="mt-1 text-xs text-ink/50">Stok ${p.stock} ${esc(p.unit)}</p>
                <div class="mt-auto flex items-center justify-between gap-2 pt-4">
                    <p class="font-display text-lg font-semibold text-leaf-700">${p.price_label}<span class="font-sans text-xs font-medium text-ink/50">/${esc(p.unit)}</span></p>
                    <button type="button" data-add-to-cart="${p.id}"
                            class="animate-wiggle grid h-10 w-10 place-items-center rounded-full bg-leaf-700 text-lg text-cream transition hover:bg-leaf-600"
                            aria-label="Tambah ${esc(p.name)} ke keranjang">＋</button>
                </div>
            </div>
        </article>`;
    }

    function renderPagination(meta) {
        pagiWrap.innerHTML = '';
        if (meta.last_page <= 1) return;

        const btn = (label, page, disabled = false, active = false) => `
            <button type="button" data-page="${page}" ${disabled ? 'disabled' : ''}
                class="min-w-10 rounded-full border-2 px-3.5 py-2 text-sm font-bold transition
                ${active ? 'border-leaf-700 bg-leaf-700 text-cream' : 'border-ink/10 bg-white hover:border-leaf-400'}
                ${disabled ? 'opacity-40' : ''}">${label}</button>`;

        let html = btn('←', meta.current_page - 1, meta.current_page === 1);
        for (let i = 1; i <= meta.last_page; i++) {
            html += btn(i, i, false, i === meta.current_page);
        }
        html += btn('→', meta.current_page + 1, meta.current_page === meta.last_page);
        pagiWrap.innerHTML = html;
    }

    async function load({ updateUrl = true } = {}) {
        skeleton();
        if (updateUrl) pushUrl();
        syncControls();

        const p = new URLSearchParams({
            q: state.q,
            kategori: state.kategori,
            harga_maks: state.harga_maks,
            sort: state.sort,
            page: state.page,
        });

        try {
            const res = await fetch(`${API_URL}?${p}`, { headers: { Accept: 'application/json' } });
            const json = await res.json();

            if (!json.data.length) {
                grid.innerHTML = '';
                empty.classList.remove('hidden');
                info.textContent = 'Tidak ada hasil.';
                pagiWrap.innerHTML = '';
                return;
            }

            empty.classList.add('hidden');
            grid.innerHTML = json.data.map(cardHtml).join('');
            info.textContent = `Menampilkan ${json.data.length} dari ${json.meta.total} produk · halaman ${json.meta.current_page}/${json.meta.last_page}`;
            renderPagination(json.meta);
        } catch (e) {
            grid.innerHTML = '';
            info.textContent = 'Gagal memuat produk. Coba muat ulang halaman.';
        }
    }

    // ---------- Event listeners ----------
    let debounce;
    inputQ.addEventListener('input', () => {
        clearTimeout(debounce);
        debounce = setTimeout(() => {
            state.q = inputQ.value.trim();
            state.page = 1;
            load();
        }, 350);
    });

    inputHrg.addEventListener('input', () => (outHrg.textContent = fmt(inputHrg.value)));
    inputHrg.addEventListener('change', () => {
        state.harga_maks = inputHrg.value;
        state.page = 1;
        load();
    });

    selSort.addEventListener('change', () => {
        state.sort = selSort.value;
        state.page = 1;
        load();
    });

    chips.forEach((chip) =>
        chip.addEventListener('click', () => {
            state.kategori = chip.dataset.kategori;
            state.page = 1;
            load();
        })
    );

    pagiWrap.addEventListener('click', (e) => {
        const b = e.target.closest('[data-page]');
        if (!b || b.disabled) return;
        state.page = parseInt(b.dataset.page, 10);
        load();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Tombol back/forward browser tetap berfungsi (ciri khas SPA yang baik)
    window.addEventListener('popstate', () => {
        const p = new URLSearchParams(location.search);
        state.q = p.get('q') || '';
        state.kategori = p.get('kategori') || '';
        state.harga_maks = p.get('harga_maks') || inputHrg.max;
        state.sort = p.get('sort') || 'terbaru';
        state.page = parseInt(p.get('page') || '1', 10);
        load({ updateUrl: false });
    });

    // Muat pertama kali
    load({ updateUrl: false });
})();
</script>
@endpush
