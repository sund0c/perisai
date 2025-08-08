@extends('adminlte::page')

@section('title', 'Edit Indikator')

@section('content_header')
    <h1>Edit Indikator untuk: {{ $indikator->fungsiStandar->nama }}</h1>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oops!</strong> Ada kesalahan saat menyimpan data:<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sk.indikator.update', $indikator->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="indikator">Indikator</label>
                    <textarea name="indikator" id="indikator" class="form-control" required>{{ old('indikator', $indikator->indikator) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="tujuan">Tujuan</label>
                    <textarea name="tujuan" id="tujuan" class="form-control">{{ old('tujuan', $indikator->tujuan) }}</textarea>
                </div>
                <div class="form-group">
                    <label for="urutan">Urutan</label>
                    <input type="number" name="urutan" id="urutan" class="form-control"
                        value="{{ old('urutan', $indikator->urutan) }}">
                </div>
                <a href="{{ route('sk.indikator.index', $indikator->fungsi_standar_id) }}"
                    class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Update</button>
    </form>
    </div>



    </div>

@endsection
