@extends('adminlte::page')

@php
    $tahunAktif = \App\Models\Periode::where('status', 'open')->first();
@endphp

@if(isset($tahunAktif))
    <span>PERIODE {{ $tahunAktif->tahun }}</span>
@endif



@section('content')
    <p>Halaman test untuk navbar kiri</p>
@endsection
