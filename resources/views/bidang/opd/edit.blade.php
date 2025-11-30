@extends('adminlte::page')

@section('title', 'Edit Aset')

@section('content_header')
    <h1>[{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }} - Edit</h1>
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
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            Terjadi kegagalan. Silakan periksa kembali isian Anda.
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('bidang.opd.update', ['aset' => $aset->uuid]) }}" method="POST">
                @csrf
                @method('PUT')

                @php
                    $skip = ['kenirsangkalan', 'keaslian'];
                @endphp

                @foreach ($fieldList as $field)
                    @continue(in_array($field, $skip))

                    @includeIf('opd.aset.fields.' . $field, [
                        'options' => $fieldOptions,
                        'aset' => $aset,
                    ])
                @endforeach


                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                <a href="{{ route('bidang.opd.view',['opd' => $idopd]) }}"
                    class="btn btn-sm btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection
