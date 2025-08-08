@extends('adminlte::page')

@section('title', 'Dashboard OPD')

@section('content_header')
    <h1>Dashboard Admin</h1>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
        </span>
    </li>
@endsection


@section('content')
    <p>Selamat datang, {{ auth()->user()->name }} dari OPD {{ auth()->user()->opd->namaopd }}</p>
@endsection



