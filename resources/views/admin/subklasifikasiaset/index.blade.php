@extends('adminlte::page')

@section('title', 'Subklasifikasi Aset')

@section('content_header')
    <h1>Sub Klas Aset dari <span class="text-primary">[ {{ $klasifikasi->klasifikasiaset }} ]</span></h1>
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
            <a href="{{ route('klasifikasiaset.index') }}" class="btn btn-secondary mb-3 me-2">
                ← Kembali
            </a>
            <a href="{{ route('subklasifikasiaset.create', $klasifikasi->id) }}" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Tambah Sub Klas
            </a>
            <a href="{{ route('subklasifikasiaset.export.pdf', $klasifikasi->id) }}" target="_blank"
                class="btn btn-danger mb-3">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <table id="subklasifikasiTable" class="table table-bordered table-hover"
                style="width:100%; table-layout: fixed;">

                <thead>
                    <tr>
                        <th>Sub Klasifikasi</th>
                        <th>Penjelasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subklasifikasi as $item)
                        <tr>
                            <td>{{ $item->subklasifikasiaset }}</td>
                            <td>{{ $item->penjelasan }}</td>
                            <td>
                                <div class="d-flex" style="gap: 5px;">
                                    <a href="{{ route('subklasifikasiaset.edit', $item->id) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('subklasifikasiaset.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
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
            $('#subklasifikasiTable').DataTable({
                autoWidth: false,
                columnDefs: [{
                        width: "300px",
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
