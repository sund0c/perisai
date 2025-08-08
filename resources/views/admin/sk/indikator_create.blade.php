@extends('adminlte::page')

@section('title', 'Tambah Indikator')

@section('content_header')
    <h1>Tambah Indikator untuk: {{ $fungsiStandar->nama }}</h1>
@stop

@section('content')
    <form action="{{ route('sk.indikator.store', ['fungsi' => $fungsiStandar->id]) }}" method="POST">

        @csrf
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label>Indikator</label>
                    <textarea name="indikator" class="form-control" required>{{ old('indikator') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Tujuan</label>
                    <textarea name="tujuan" class="form-control">{{ old('tujuan') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="urutan">Urutan</label>
                    <input type="number" name="urutan" id="urutan" class="form-control" value="{{ old('urutan', 0) }}">
                </div>
                <a href="{{ route('sk.indikator.index', $fungsiStandar->id) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Simpan</button>
    </form>
    </div>



    </div>

@endsection
