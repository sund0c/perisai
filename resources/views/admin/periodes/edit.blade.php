@extends('adminlte::page')

@section('title', 'Edit Periode Tahun')

@section('content_header')
    <h1>Edit Periode Tahun</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('periodes.update', $periode->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="tahun">Tahun</label>
                <input type="number" name="tahun" class="form-control" value="{{ old('tahun', $periode->tahun) }}" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" class="form-control" required>
                    <option value="open" {{ old('status', $periode->status) == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ old('status', $periode->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div class="form-group">
                <label for="kunci">Status</label>
                <select name="kunci" class="form-control" required>
                    <option value="open" {{ old('kunci', $periode->kunci) == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="locked" {{ old('kunci', $periode->kunci) == 'locked' ? 'selected' : '' }}>Locked</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('periodes.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
