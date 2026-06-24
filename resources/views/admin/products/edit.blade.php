@extends('layouts.admin')

@section('title', 'Edit Produk')

@section('content')
{{-- UPDATE (kriteria CRUD: Update) --}}
<form method="POST" action="{{ route('admin.produk.update', $product) }}" enctype="multipart/form-data" class="animate-fade-up">
    @csrf
    @method('PUT')
    @include('admin.products._form')
</form>
@endsection
