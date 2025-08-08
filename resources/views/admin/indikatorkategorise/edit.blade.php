@extends('adminlte::page')

@section('title', 'Edit Indikator Kategori SE')

@section('content_header')
    <h1>Edit Indikator Kategori SE</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.indikatorkategorise.update', $indikator->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Kode</label>
                    <input type="text" name="kode" class="form-control" value="{{ $indikator->kode }}" required>
                </div>

                <div class="form-group">
                    <label>Pertanyaan</label>
                    <textarea name="pertanyaan" class="form-control" rows="3" required>{{ $indikator->pertanyaan }}</textarea>
                </div>

                <div class="form-group">
                    <label>Opsi A</label>
                    <input type="text" name="opsi_a" class="form-control" value="{{ $indikator->opsi_a }}" required>
                </div>

                <div class="form-group">
                    <label>Opsi B</label>
                    <input type="text" name="opsi_b" class="form-control" value="{{ $indikator->opsi_b }}" required>
                </div>

                <div class="form-group">
                    <label>Opsi C</label>
                    <input type="text" name="opsi_c" class="form-control" value="{{ $indikator->opsi_c }}" required>
                </div>

                <div class="form-group">
                    <label>Urutan</label>
                    <input type="number" name="urutan" class="form-control" value="{{ $indikator->urutan }}" required>
                </div>

                <button type="submit" class="btn btn-success">Update</button>
                <a href="{{ route('admin.indikatorkategorise.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection
