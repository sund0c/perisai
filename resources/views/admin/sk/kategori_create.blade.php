@extends('adminlte::page')

@section('title', 'Tambah Kategori')

@section('content_header')
    <h1>Tambah Kategori</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('sk.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Kategori</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <a href="{{ route('sk.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Simpan</button>

            </form>
        </div>
    </div>
@endsection
