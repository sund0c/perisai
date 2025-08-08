@extends('adminlte::page')
@section('title', 'Edit Sub Klasifikasi Aset')

@section('content_header')
    <h1>Edit Sub Klas dari <span class="text-primary">[ {{ $klasifikasi->klasifikasiaset }} ]</span></h1>
@endsection
@section('content')

<div class="card">
    <div class="card-body">
<form action="{{ route('subklasifikasiaset.update', $sub->id) }}" method="POST">
    @csrf @method('PUT')

    <div class="mb-3">
        <label>Nama Subklasifikasi</label>
        <input type="text" name="subklasifikasiaset" class="form-control" value="{{ $sub->subklasifikasiaset }}" required>
    </div>

    <div class="mb-3">
        <label>Penjelasan</label>
        <textarea name="penjelasan" class="form-control">{{ $sub->penjelasan }}</textarea>
    </div>

    <button type="submit" class="btn btn-success">Update</button>
    <a href="{{ route('subklasifikasiaset.index', $klasifikasi->id) }}" class="btn btn-secondary">Batal</a>
</form>
    </div>
</div>
@endsection
