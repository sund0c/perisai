@extends('adminlte::page')

@section('title', 'Klasifikasi Aset')

@section('content_header')
    <h1>Klasifikasi Aset</h1>
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
                <a href="{{ route('klasifikasiaset.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Klasifikasi
                </a>
                <a href="{{ route('klasifikasiaset.export.pdf') }}" target="_blank" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>

            <table id="klasifikasiTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th style="width: auto;">Klasifikasi Aset</th>
                        <th>Aksi</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($klasifikasis as $klasifikasi)
                        <tr>
                            <td>{{ $klasifikasi->id }}</td>
                            <td>
                                <a href="{{ route('subklasifikasiaset.index', $klasifikasi->id) }}">
                                    [{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }}
                                </a>
                            </td>
                            <td>
                                <div class="d-flex" style="gap: 5px;">

                                    <a href="{{ route('klasifikasiaset.field', $klasifikasi->id) }}"
                                        class="btn btn-success btn-sm" title="Form Aset">
                                        <i class="fas fa-check-square"></i>
                                    </a>
                                    <a href="{{ route('klasifikasiaset.edit', $klasifikasi->id) }}"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('klasifikasiaset.destroy', $klasifikasi->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Yakin hapus?')">
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
            $('#klasifikasiTable').DataTable({
                autoWidth: false,
                columnDefs: [{
                        width: "30px",
                        targets: 0
                    },
                    {
                        width: "auto",
                        targets: 1
                    },
                    {
                        width: "100px",
                        targets: 2
                    }
                ]
            });
        });
    </script>
@endsection
