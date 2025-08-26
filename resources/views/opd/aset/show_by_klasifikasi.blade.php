@extends('adminlte::page')

@section('title', 'Daftar Aset - ' . ($klasifikasi->klasifikasiaset ?? '-'))
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
            @if (($kunci ?? null) === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @endif
            :: {{ strtoupper($namaOpd ?? '-') }}
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
                <a href="{{ route('opd.aset.index') }}" class="btn btn-sm btn-secondary mb-3 me-2">
                    ← Kembali
                </a>

                {{-- WAJIB: kirim param "klasifikasiaset" --}}
                <a href="{{ route('opd.aset.export_rekap_klas', ['klasifikasiaset' => $klasifikasi]) }}"
                    class="btn btn-sm btn-danger mb-3">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>

                @if (($kunci ?? null) !== 'locked')
                    <a href="{{ route('opd.aset.create', ['klasifikasiaset' => $klasifikasi]) }}"
                        class="btn btn-sm btn-success mb-3">
                        <i class="fas fa-plus"></i> Tambah Aset
                    </a>
                @endif


            </div>

            <table id="asetTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Sub Klasifikasi Aset</th>
                        <th>Nilai Aset (CIAAN)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($asets as $aset)
                        <tr>
                            <td>{{ $aset->kode_aset }}</td>
                            <td>{{ $aset->nama_aset }}</td>
                            <td>{{ optional($aset->subklasifikasiaset)->subklasifikasiaset ?? '-' }}</td>
                            <td style="background-color: {{ $aset->warna_hexa }}; color: #fff; font-weight: bold;">
                                {{ $aset->nilai_akhir_aset }}
                            </td>
                            <td class="actions-cell align-middle text-nowrap">
                                {{-- Rute aset pakai {aset:uuid} → kirim uuid eksplisit --}}
                                <a href="{{ route('opd.aset.pdf', ['aset' => $aset->uuid]) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-file-pdf"></i>
                                </a>

                                @if (($kunci ?? null) !== 'locked')
                                    <a href="{{ route('opd.aset.edit', ['aset' => $aset->uuid]) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('opd.aset.destroy', ['aset' => $aset->uuid]) }}" method="POST"
                                        style="display:inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Yakin hapus aset ini?')"
                                            class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data aset untuk klasifikasi ini.</td>
                        </tr>
                    @endforelse
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
                        width: "140px",
                        targets: 4
                    },
                ]
            });
        });
    </script>
@endsection
