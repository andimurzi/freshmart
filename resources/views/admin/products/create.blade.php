@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
{{-- INSERT (kriteria CRUD: Insert) --}}
<form method="POST" action="{{ route('admin.produk.store') }}" enctype="multipart/form-data" class="animate-fade-up">
    @csrf
    @include('admin.products._form')
</form>
@endsection
