@extends('adminlte::page')

@section('title', 'Edit Range Aset')

@section('content_header')
    <h1>Edit Range Aset</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
    <form action="{{ route('rangeaset.update', $rangeAset->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nilai_akhir_aset">Nilai Aset</label>
            <input type="text" name="nilai_akhir_aset" class="form-control" value="{{ old('nilai_akhir_aset', $rangeAset->nilai_akhir_aset) }}" required>
        </div>

        <div class="form-group">
            <label for="warna_hexa">Warna (Hex)</label>
            <input type="color" name="warna_hexa" class="form-control" value="{{ old('warna_hexa', $rangeAset->warna_hexa) }}" required>
        </div>

        <div class="form-group">
            <label for="nilai_bawah">Nilai Bawah</label>
            <input type="number" name="nilai_bawah" class="form-control" step="0.01" value="{{ old('nilai_bawah', (int)$rangeAset->nilai_bawah) }}" required>
        </div>

        <div class="form-group">
            <label for="nilai_atas">Nilai Atas</label>
            <input type="number" name="nilai_atas" class="form-control" step="0.01" value="{{ old('nilai_atas', (int)$rangeAset->nilai_atas) }}" required>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $rangeAset->deskripsi) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('rangeaset.index') }}" class="btn btn-secondary">Batal</a>
    </form>
    </div>
</div>
@endsection