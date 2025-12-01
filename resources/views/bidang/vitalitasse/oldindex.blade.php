@extends('adminlte::page')

@section('title', 'Kategori SE')

@section('content_header')
    <h1>Kategori SE pada {{ strtoupper($namaOpd) }}</h1>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
            @if ($kunci === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @endif
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
                <a href="{{ route('klasifikasiaset.export.pdf') }}" target="_blank" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
            <table id="kategoriseTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th>Nama Aset</th>
                        <th>Sub Klasifikasi</th>
                        <th>Lokasi</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $aset)
                        <tr>
                            <td>{{ $aset->nama_aset }}</td>
                            <td>{{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</td>
                            <td>{{ $aset->lokasi }}</td>
                            <td>
                                {{ $aset->kategoriSe?->skor_total ?? 'BELUM DINILAI' }}
                            </td>
                            <td>
                                <a href="{{ route('kategorise.exportPdf', $aset->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a href="{{ route('kategorise.edit', $aset->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
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
        $('#kategoriseTable').DataTable({
          autoWidth: false,
          stateSave: true,
          columnDefs: [{
                        width: "auto",
                        targets: 0
                    },
                    {
                        width: "auto",
                        targets: 1
                    },
                    {
                        width: "100px",
                        targets: 2
                    },
                    {
                        width: "100px",
                        targets: 3
                    },
                    {
                        width: "100px",
                        targets: 4
                    },
                ]
            });
        });
    </script>
@endsection
