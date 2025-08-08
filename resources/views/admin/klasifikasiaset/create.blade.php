@extends('adminlte::page')

@section('title', 'Tambah Klasifikasi Aset')

@section('content_header')
    <h1>Tambah Klasifikasi Aset</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
<form action="{{ route('klasifikasiaset.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label>Klasifikasi Aset</label>
        <input type="text" name="klasifikasiaset" class="form-control" required>
    </div>
<div class="form-group">
        <label>Kode Klasifikasi Aset</label>
        <input type="text" name="kodeklas" class="form-control" required>
    </div>       
    <button type="submit" class="btn btn-success">Simpan</button>
    <a href="{{ route('klasifikasiaset.index') }}" class="btn btn-secondary">Kembali</a>
</form>
    </div>
</div>
@endsection
