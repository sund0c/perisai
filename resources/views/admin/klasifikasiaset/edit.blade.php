@extends('adminlte::page')

@section('title', 'Edit Klasifikasi Aset')

@section('content_header')
    <h1>Edit Klasifikasi Aset</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
<form action="{{ route('klasifikasiaset.update', $klasifikasiaset->id) }}" method="POST">
    @csrf @method('PUT')
    <div class="form-group">
        <label>Klasifikasi Aset</label>
        <input type="text" name="klasifikasiaset" class="form-control" value="{{ $klasifikasiaset->klasifikasiaset }}" required>
    </div>
<div class="form-group">
        <label>Kode Klasifikasi Aset</label>
        <input type="text" name="kodeklas" class="form-control" value="{{ $klasifikasiaset->kodeklas }}" required>
    </div>    
    <button type="submit" class="btn btn-primary">Update</button>
    <a href="{{ route('klasifikasiaset.index') }}" class="btn btn-secondary">Kembali</a>
</form>
    </div>
</div>
@endsection
