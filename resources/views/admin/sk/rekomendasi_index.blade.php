@extends('adminlte::page')

@section('title', $indikator->indikator)

@section('content_header')
    <h1>{{ $indikator->indikator }}</h1>
@stop

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
            <a href="{{ route('sk.indikator.index', $indikator->fungsi_standar_id) }}" class="btn btn-secondary mb-3 me-2">
                ← Kembali
            </a>
            <a href="{{ route('sk.rekomendasi.create', $indikator->id) }}" class="btn btn-primary mb-3">
                + Tambah Rekomendasi
            </a>
            <a href="{{ route('sk.rekomendasipdf', $indikator->id) }}" target="_blank" class="btn btn-danger mb-3">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>

            <table id="rekomendasistandardTable" class="table table-bordered table-hover"
                style="width:100%; table-layout: fixed;">
                <thead class="thead-light">
                    <tr>
                        <th width="20px">#</th>
                        <th>Rekomendasi</th>
                        <th>Bukti Dukung</th>
                        <th width="100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekomendasis as $index => $rek)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $rek->rekomendasi }}</td>
                            <td>{{ $rek->buktidukung }}</td>
                            <td>
                                <a href="{{ route('sk.rekomendasi.edit', $rek->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sk.rekomendasi.destroy', $rek->id) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada rekomendasi.</td>
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
            $('#rekomendasistandardTable').DataTable({
                autoWidth: false

            });
        });
    </script>
@endsection
