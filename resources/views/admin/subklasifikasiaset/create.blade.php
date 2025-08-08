@extends('adminlte::page')

@section('title', 'Tambah Sub Klasifikasi Aset')

@section('content_header')
    <h1>Tambah Sub Klas dari <span class="text-primary">[ {{ $klasifikasi->klasifikasiaset }} ]</span></h1>
@endsection

@section('content')

<div class="card">
    <div class="card-body">

<form action="{{ route('subklasifikasiaset.store') }}" method="POST">
    @csrf
    <input type="hidden" name="klasifikasi_aset_id" value="{{ $klasifikasi->id }}">


    <div class="mb-3">
        <label>Nama Sub Klasifikasi Aset</label>
        <input type="text" name="subklasifikasiaset" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Penjelasan</label>
        <textarea name="penjelasan" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="{{ route('subklasifikasiaset.index', $klasifikasi->id) }}" class="btn btn-secondary">Batal</a>
</form>
    </div>
</div>
@endsection
