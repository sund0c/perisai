@extends('adminlte::page')

@section('title', $indikator->indikator)

@section('content_header')
    <h1>{{ $indikator->indikator }}</h1>
@stop
@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
        </span>
    </li>
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Rekomendasi</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sk.rekomendasi.update', $rekomendasi->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="form-group">
                    <label for="rekomendasi">Rekomendasi</label>
                    <textarea name="rekomendasi" id="rekomendasi" class="form-control" rows="4" required>{{ old('rekomendasi', $rekomendasi->rekomendasi) }}</textarea>
                </div>
                <div class="form-group">
                    <label for="buktidukung">Bukti Dukung</label>
                    <textarea name="buktidukung" id="buktidukung" class="form-control" rows="3">{{ old('buktidukung', $rekomendasi->buktidukung) }}</textarea>
                </div>

                <a href="{{ route('sk.rekomendasi.index', $rekomendasi->standar_indikator_id) }}"
                    class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Update</button>
            </form>
        </div>
    </div>
@endsection
