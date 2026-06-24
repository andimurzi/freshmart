@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')

<div class="animate-fade-up mx-auto max-w-lg rounded-3xl border-2 border-ink/10 bg-white p-6">
    <h2 class="font-display text-xl font-semibold">✏️ Edit: {{ $category->name }}</h2>

    @if ($errors->any())
        <div class="mt-4 rounded-2xl border-2 border-tomato/30 bg-tomato/10 p-3 text-xs font-semibold text-tomato">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.kategori.update', $category) }}" class="mt-4 space-y-4">
        @csrf
        @method('PUT')
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Nama kategori</span>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                   class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Ikon (emoji)</span>
            <input type="text" name="icon" value="{{ old('icon', $category->icon) }}" required maxlength="10"
                   class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">
        </label>
        <label class="block">
            <span class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-ink/50">Deskripsi (opsional)</span>
            <textarea name="description" rows="2"
                      class="w-full rounded-2xl border-2 border-ink/10 bg-cream px-4 py-3 text-sm font-semibold outline-none transition focus:border-leaf-500">{{ old('description', $category->description) }}</textarea>
        </label>
        <button type="submit" class="w-full rounded-full bg-leaf-700 py-3 font-bold text-cream shadow-card transition hover:-translate-y-0.5 hover:bg-leaf-600">💾 Simpan Perubahan</button>
        <a href="{{ route('admin.kategori.index') }}" class="block text-center text-sm font-bold text-ink/50 hover:text-ink">← Kembali</a>
    </form>
</div>

@endsection
