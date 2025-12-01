@extends('adminlte::page')

@section('title',
    'Kategori Kepgub Bali No. 584/03-E/HK/2024
    ')

@section('content_header')
    <h1>Kepgub Bali No. 584/03-E/HK/2024 Pedoman Manajemen Keamanan Informasi</h1>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
        </span>
    </li>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex mb-3" style="gap: 10px;">
                <a href="{{ route('sk.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Kategori
                </a>
                <a href="{{ route('sk.kategoripdf') }}" target="_blank" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>

            <table id="kategoriTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th style="width: 30px;">ID</th>
                        <th>Nama Kategori</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kategoris as $kategori)
                        <tr>
                            <td>{{ $kategori->id }}</td>
                            <td>
                                <a href="{{ route('sk.fungsistandar.index', $kategori->id) }}">
                                    {{ $kategori->nama }}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex" style="gap: 5px;">
                                    <a href="{{ route('sk.edit', $kategori->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('sk.destroy', $kategori->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('#kategoriTable').DataTable({
                autoWidth: false,
                stateSave: true
            });
        });
    </script>
@endsection
