@extends('adminlte::page')

@section('title', 'Indikator - ' . $fungsiStandar->nama)

@section('content_header')
    <h1>Indikator untuk aspek: {{ $fungsiStandar->nama }}</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <a href="{{ route('sk.fungsistandar.index', $fungsiStandar->kategori_id) }}" class="btn btn-secondary mb-3 me-2">
                ← Kembali
            </a>
            <a href="{{ route('sk.indikator.create', $fungsiStandar->id) }}" class="btn btn-primary mb-3">
                + Tambah Indikator
            </a>
            <a href="{{ route('sk.indikatorpdf', $fungsiStandar->id) }}" target="_blank" class="btn btn-danger mb-3">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>

            <table id="indikatorTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th style="width: 50px;">Urutan</th>
                        <th>Indikator</th>
                        <th>Tujuan</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($indikators as $i => $indikator)
                        <tr>
                            <td align="center">{{ $indikator->urutan }}</td>
                            <td>
                                <a href="{{ route('sk.rekomendasi.index', $indikator->id) }}">
                                    {{ $indikator->indikator }}
                                </a>
                            </td>
                            <td>{{ $indikator->tujuan }}</td>
                            <td>
                                <a href="{{ route('sk.indikator.edit', $indikator->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sk.indikator.destroy', $indikator->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin ingin menghapus indikator ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada indikator.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('#indikatorTable').DataTable({
                autoWidth: false

            });
        });
    </script>
@endsection
