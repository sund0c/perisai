@extends('adminlte::page')

@section('title', 'Edit Kategori')

@section('content_header')
    <h1>Edit Kategori</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('sk.update', $kategori->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label>Kategori</label>
                    <input type="text" name="nama" class="form-control" value="{{ $kategori->nama }}" required>
                </div>
                <a href="{{ route('sk.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Update</button>



            </form>
        </div>
    </div>
@endsection
