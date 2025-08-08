@extends('adminlte::page')

@section('title', 'Indikator - ' . $fungsi->nama)

@section('content_header')
    <h1>Indikator Keamanan: {{ $fungsi->nama }}</h1>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('sk.fungsistandar.update', $fungsi->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label>Fungsi</label>
                    <input type="text" name="nama" class="form-control" value="{{ $fungsi->nama }}" required>
                </div>
                <div class="form-group">
                    <label for="urutan">Urutan</label>
                    <input type="number" name="urutan" id="urutan" class="form-control"
                        value="{{ old('urutan', $fungsi->urutan) }}">
                </div>
                <a href="{{ route('sk.fungsistandar.index', $fungsi->id) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Simpan</button>

            </form>
        </div>
    </div>
@endsection
