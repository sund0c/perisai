@extends('adminlte::page')

@section('title', 'Edit Aset')

@section('content_header')
    <h1>[{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }} - Edit
    </h1>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
            :: {{ strtoupper($namaOpd) }}
        </span>
    </li>
@endsection

@section('content')
    {{-- Error validasi (otomatis dari $request->validate / withErrors) --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Terjadi kegagalan. Silakan periksa kembali isian Anda.
            {{-- <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
        </div>
    @endif

    <div class="card">
        <div class="card-body">





            <form action="{{ route('opd.aset.update', ['aset' => $aset->uuid]) }}" method="POST">

                @csrf
                @method('PUT')

                {{-- Field dinamis --}}
                @foreach ($fieldList as $field)
                    @includeIf('opd.aset.fields.' . $field, ['aset' => $aset])
                @endforeach

                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                <a href="{{ route('opd.aset.show_by_klasifikasi', $klasifikasi->id) }}"
                    class="btn btn-sm btn-secondary">Batal</a>
            </form>

        </div>
    </div>
@endsection
