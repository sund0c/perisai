@extends('adminlte::page')

@section('title', 'Daftar Aset - ' . $klasifikasi->klasifikasiaset)

@section('content_header')
    <h1>[{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }}</h1>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
            @if (($kunci ?? 'locked') === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @else
                <i class="fas fa-lock-open text-success ml-1" title="Terbuka"></i>
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
                <a href="{{ route('bidang.aset.index') }}" class="btn btn-secondary btn-sm mb-3 me-2">
                    ← Kembali
                </a>
                <a href="{{ route('bidang.aset.export_rekap_klas', $klasifikasi->id) }}" class="btn btn-danger btn-sm mb-3">

                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>

            </div>

            <table id="asetTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Sub Klasifikasi</th>
                        <th>Nilai</th>
                        <th>Detil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asets as $aset)
                        <tr>
                            <td>{{ $aset->kode_aset }}</td>
                            <td>{{ $aset->nama_aset }}</td>
                            <td>{{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</td>
                            <td style="background-color: {{ $aset->warna_hexa }}; color: #fff; font-weight: bold;">
                                {{ $aset->nilai_akhir_aset }}
                            </td>

                            <td align="center">
                                <a href="{{ route('bidang.aset.pdf', $aset->id) }}" class="btn btn-sm btn-danger"><i
                                        class="fas fa-file-pdf"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    @if ($asets->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data aset untuk klasifikasi ini.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('#asetTable').DataTable({
                autoWidth: false,
                columnDefs: [{
                        width: "100px",
                        targets: 0
                    },
                    {
                        width: "300px",
                        targets: 1
                    },
                    {
                        width: "auto",
                        targets: 2
                    },
                    {
                        width: "100px",
                        targets: 3
                    },
                    {
                        width: "50px",
                        targets: 4
                    },
                ]
            });
        });
    </script>
@endsection
