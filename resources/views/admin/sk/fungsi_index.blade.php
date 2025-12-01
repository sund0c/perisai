@extends('adminlte::page')

@section('title', 'Aspek - ' . $kategori->nama)

@section('content_header')
    <h1>Standar untuk kategori: {{ $kategori->nama }}</h1>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <a href="{{ route('sk.index') }}" class="btn btn-secondary mb-3 me-2">
                ← Kembali
            </a>
            <a href="{{ route('sk.fungsistandar.create', $kategori->id) }}" class="btn btn-primary mb-3">
                <i class="fas fa-plus"></i> Tambah Aspek
            </a>
            <a href="{{ route('sk.fungsistandarpdf', $kategori->id) }}" target="_blank" class="btn btn-danger mb-3">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>

            <table id="fungsiTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">

                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th>Aspek</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kategori->fungsi as $fungsi)
                        <tr>
                            <td align="center">{{ $fungsi->urutan }}</td>
                            <td>
                                <a href="{{ route('sk.indikator.index', $fungsi->id) }}">
                                    {{ $fungsi->nama }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('sk.fungsistandar.edit', $fungsi->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sk.fungsistandar.destroy', $fungsi->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
            $('#fungsiTable').DataTable({
                autoWidth: false,
                stateSave: true,
                pageLength: 50
            });
        });
    </script>
@endsection
