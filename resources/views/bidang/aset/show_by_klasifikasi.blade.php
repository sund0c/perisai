@extends('adminlte::page')

@section('title', 'Daftar Aset - ' . $klasifikasi->klasifikasiaset)
<style>
    .matik-list ul {
        margin: 0;
        padding-left: 1.2rem;
        /* default untuk nested */
    }

    /* level pertama */
    .matik-list>ul {
        padding-left: 1em;
        /* mepet kiri */
        list-style-type: disc;
        /* bullet bulat */
    }

    /* level kedua */
    .matik-list>ul>li>ul {
        padding-left: 1.5rem;
        list-style-type: square;
        font-size: 0.8em;
    }

    /* Kolom aksi: rapikan tanpa mengecilkan semua tombol */
    .actions-cell {
        white-space: nowrap;
        /* cegah wrap → baris tak jadi tinggi */
        vertical-align: middle;
        /* tombol di tengah vertikal */
    }

    /* Atur jarak antar tombol tanpa mengubah ukuran tombol global */
    .actions-cell .btn+.btn,
    .actions-cell .btn+form,
    .actions-cell form+.btn,
    .actions-cell form+form {
        margin-left: .25rem;
    }

    /* Pastikan form tetap inline & tanpa margin */
    .actions-cell form {
        display: inline;
        margin: 0;
    }
</style>
@section('content_header')
    <h1>[{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }}</h1>
    <div style="line-height:1.2; font-size: 0.9em">
        {{ optional($subs)->pluck('subklasifikasiaset')->implode(', ') ?: '-' }}
    </div>
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
                        <th>Sub Klasifikasi Aset</th>
                        <th>Pemilik Risiko</th>
                        <th>Nilai Aset (CIA)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asets as $aset)
                        <tr>
                            <td>{{ $aset->kode_aset }}</td>
                            <td>{{ $aset->nama_aset }}</td>
                            <td>{{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</td>
                            <td>{{ $aset->opd->namaopd }}</td>
                            <td style="background-color: {{ $aset->warna_hexa }}; color: #fff; font-weight: bold;">
                                {{ $aset->nilai_akhir_aset }}
                            </td>

                            <td align="center" class="actions-cell">
                                <a href="{{ route('bidang.aset.edit', $aset->uuid) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('bidang.aset.pdf', $aset->id) }}" class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
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
            <br>
            <b>Keterangan Sub Klasifikasi Aset</b>
            <div class="matik-list" style="font-size:0.9em">
                @if (!empty($subs) && $subs->isNotEmpty())
                    <ul>
                        @foreach ($subs as $sub)
                            <li><b>{{ $sub->subklasifikasiaset }} :</b> {{ $sub->penjelasan }}</li>
                        @endforeach
                    </ul>
                @else
                    -
                @endif
            </div>

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
                        width: "100px",
                        targets: 4
                    },
                    {
                        width: "50px",
                        targets: 5
                    },
                ]
            });
        });
    </script>
@endsection
