@extends('adminlte::page')

@section('title', 'Edit OPD')

@section('content_header')
    <h1>Edit OPD</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
    <form action="{{ route('opd.update', $opd->id) }}" method="POST">
        @csrf
        @method('PUT')
                <div class="form-group">
                    <label for="namaopd">Nama OPD</label>
                    <input type="text" name="namaopd" value="{{ $opd->namaopd }}" class="form-control @error('namaopd') is-invalid @enderror" required>
                    @error('namaopd')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
                
                <button type="submit" class="btn btn-success">Perbarui</button>
                <a href="{{ route('opd.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
        </div>
</div>
@endsection
