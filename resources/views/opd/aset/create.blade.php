@extends('adminlte::page')

@section('title', 'Tambah Aset')

@section('content_header')
    <h1>{{ strtoupper($namaOpd) }} : [{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }} - Tambah Aset</h1>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
        </span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-body">



    <form action="{{ route('opd.aset.store', $klasifikasi->id) }}" method="POST">
        @csrf

            {{-- Field dinamis --}}
            @foreach($fieldList as $field)
                @includeIf('opd.aset.fields.' . $field)
            @endforeach


        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('opd.aset.show_by_klasifikasi', $klasifikasi->id) }}" class="btn btn-secondary">Batal</a>
    </form>
    </div>
</div>
@endsection
