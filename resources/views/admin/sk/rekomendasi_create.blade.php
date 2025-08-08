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
            <h5 class="mb-0">Tambah Rekomendasi</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sk.rekomendasi.store', $indikator->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="rekomendasi">Rekomendasi</label>
                    <textarea name="rekomendasi" id="rekomendasi" class="form-control" rows="4" required>{{ old('rekomendasi') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="buktidukung">Bukti Dukung yang bisa digunakan</label>
                    <textarea name="buktidukung" id="buktidukung" class="form-control" rows="3">{{ old('buktidukung') }}</textarea>
                </div>

                <a href="{{ route('sk.rekomendasi.index', $indikator->id) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Simpan</button>
            </form>
        </div>
    </div>
@endsection
