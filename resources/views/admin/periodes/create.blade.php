@extends('adminlte::page')

@section('title', 'Tambah Periode Tahun')

@section('content_header')
    <h1>Tambah Periode Tahun</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('periodes.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="tahun">Tahun</label>
                <input type="number" name="tahun" class="form-control" value="{{ old('tahun') }}" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" class="form-control" required>
                    <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('periodes.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
