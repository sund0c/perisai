@extends('adminlte::page')

@section('title', 'Tambah Fungsi')

@section('content_header')
    <h1>Tambah Fungsi untuk {{ $kategori->nama }}</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('sk.fungsistandar.store', $kategori->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Fungsi</label>
                    <input type="text" name="nama" class="form-control">
                </div>
                <div class="form-group">
                    <label for="urutan">Urutan</label>
                    <input type="number" name="urutan" id="urutan" class="form-control" value="{{ old('urutan', 0) }}">
                </div>
                <a href="{{ route('sk.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Simpan</button>

            </form>
        </div>
    </div>
@endsection
