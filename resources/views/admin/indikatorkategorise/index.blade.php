@extends('adminlte::page')

@section('title', 'Indikator Kategori SE')

@section('content_header')
    <h1>Indikator Kategori SE</h1>
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
                <a href="{{ route('admin.indikatorkategorise.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Indikator
                </a>
                <a href="{{ route('indikatorkategorise.export.pdf') }}" target="_blank" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
            <table id="indikatorseTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pertanyaan</th>
                        <th>Opsi A</th>
                        <th>Opsi B</th>
                        <th>Opsi C</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($indikator as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->pertanyaan }}</td>
                            <td>{{ $item->opsi_a }} ({{ $item->nilai_a }})</td>
                            <td>{{ $item->opsi_b }} ({{ $item->nilai_b }})</td>
                            <td>{{ $item->opsi_c }} ({{ $item->nilai_c }})</td>
                            <td>
                                <a href="{{ route('admin.indikatorkategorise.edit', $item->id) }}"
                                    class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.indikatorkategorise.destroy', $item->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
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
            $('#indikatorseTable').DataTable({
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
                        width: "auto",
                        targets: 2
                    },
                    {
                        width: "auto",
                        targets: 3
                    },
                    {
                        width: "auto",
                        targets: 4
                    },
                    {
                        width: "100px",
                        targets: 5
                    }
                ]
            });
        });
    </script>
@endsection
