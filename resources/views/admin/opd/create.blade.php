@extends('adminlte::page')

@section('title', 'Tambah OPD')

@section('content_header')
    <h1>Tambah OPD</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
    <form action="{{ route('opd.store') }}" method="POST">
        @csrf
                <div class="form-group">
                    <label for="namaopd">Nama OPD</label>
                    <input type="text" name="namaopd" class="form-control @error('namaopd') is-invalid @enderror" required>
                    @error('namaopd')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
                
    <button type="submit" class="btn btn-success">Simpan</button>
   <a href="{{ route('opd.index') }}" class="btn btn-secondary">Batal</a>             

    </form>
        </div>
</div>
@endsection
