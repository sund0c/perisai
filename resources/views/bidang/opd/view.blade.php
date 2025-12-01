@extends('adminlte::page')

@section('title', 'Daftar Aset OPD/UPTD')
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
    <h1>
        {{ $opd->namaopd }}
    </h1>
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
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex mb-3" style="gap: 10px;">
                <a href="{{ route('bidang.opd.index') }}" class="btn btn-sm btn-secondary mb-3 me-2">
                    ← Kembali
                </a>

                {{-- WAJIB: kirim param "klasifikasiaset" --}}
                {{-- <a href="{{ route('opd.aset.export_rekap_klas', ['klasifikasiaset' => $klasifikasi]) }}"
                    class="btn btn-sm btn-danger mb-3">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>

                @if (($kunci ?? null) !== 'locked')
                    <a href="{{ route('opd.aset.create', ['klasifikasiaset' => $klasifikasi]) }}"
                        class="btn btn-sm btn-success mb-3">
                        <i class="fas fa-plus"></i> Tambah Aset
                    </a>
                @endif --}}


            </div>

            <table id="asetTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Aset</th>
                        <th>Klasifikasi Aset</th>
                        <th>Sub Klasifikasi Aset</th>
                        <th>Nilai Aset (CIA)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no=1;
                    @endphp
                    @forelse ($asetopd as $aset)
                        <tr>
                            {{-- <td>{{ $aset->kode_aset }}</td> --}}
                            <td>{{ $no++ }}</td>
                            <td>{{ $aset->nama_aset }}
                                <p class="small" style="margin-bottom: 0">{{ $aset->keterangan }}</p>
                            </td>
                            <td>{{ $aset->klasifikasi->klasifikasiaset}}</td>
                            <td>{{ optional($aset->subklasifikasiaset)->subklasifikasiaset ?? '-' }}</td>

                            <td style="background-color: {{ $aset->warna_hexa }}; color: #fff; font-weight: bold;">
                                {{ $aset->nilai_akhir_aset }}
                            </td>

                            <td class="actions-cell align-middle text-nowrap">
                               <a href="{{ route('bidang.opd.edit', $aset->uuid) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('bidang.opd.pdf', $aset->id) }}" class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data aset untuk klasifikasi ini.</td>
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
            $('#asetTable').DataTable({
                autoWidth: false,
                stateSave: true,
                pageLength: 100,
                columnDefs: [{
                        width: "10px",
                        targets: 0
                    },
                    {
                        width: "auto",
                        targets: 1
                    },
                    {
                        width: "200px",
                        targets: 2
                    },
                    {
                        width: "200px",
                        targets: 3
                    },
                    {
                        width: "100px",
                        targets: 4
                    },
                    {
                        width: "140px",
                        targets: 5
                    },
                ]
            });
        });
    </script>
@endsection
