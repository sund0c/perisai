@extends('adminlte::page')

@section('title', 'Daftar Aset - ' . ($klasifikasi->klasifikasiaset ?? '-'))
<style>
    .matik-list ul {
        margin: 0;
        padding-left: 1.2rem;
    }

    .matik-list>ul {
        padding-left: 1em;
        list-style-type: disc;
    }

    .matik-list>ul>li>ul {
        padding-left: 1.5rem;
        list-style-type: square;
        font-size: 0.8em;
    }

    .actions-cell {
        white-space: nowrap;
        vertical-align: middle;
    }

    .actions-cell .btn+.btn,
    .actions-cell .btn+form,
    .actions-cell form+.btn,
    .actions-cell form+form {
        margin-left: .25rem;
    }

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
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

                {{-- WAJIB: kirim param "klasifikasiaset" --}}
                <a href="{{ route('opd.aset.export_rekap_klas', ['klasifikasiaset' => $klasifikasi]) }}"
                    class="btn btn-sm btn-danger mb-3">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>

                <button type="button" class="btn btn-sm btn-outline-success mb-3" data-toggle="modal"
                    data-target="#excelModal">
                    <i class="fas fa-file-excel"></i> Import
                </button>

                @if (($kunci ?? null) !== 'locked')
                    <a href="{{ route('opd.aset.create', ['klasifikasiaset' => $klasifikasi]) }}"
                        class="btn btn-sm btn-success mb-3">
                        <i class="fas fa-plus"></i> Tambah Aset
                    </a>
                @endif
            </div>

            {{-- Modal aksi Excel --}}
            <div class="modal fade" id="excelModal" tabindex="-1" role="dialog" aria-labelledby="excelModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="excelModalLabel">Aksi Excel</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex flex-column" style="gap: 10px;">
                                <a href="{{ route('opd.aset.template_excel', ['klasifikasiaset' => $klasifikasi]) }}"
                                    class="btn btn-outline-success">
                                    <i class="fas fa-file-excel"></i> Template Excel
                                </a>
                                <a href="{{ route('opd.aset.export_excel', ['klasifikasiaset' => $klasifikasi]) }}"
                                    class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                                @if (($kunci ?? null) !== 'locked')
                                    <form action="{{ route('opd.aset.import_excel', ['klasifikasiaset' => $klasifikasi]) }}"
                                        method="POST" enctype="multipart/form-data" class="p-3 border rounded"
                                        style="background: #f8f9fa;">
                                        @csrf
                                        <div class="form-group mb-2">
                                            <label for="file_import" class="mb-2 font-weight-bold d-flex align-items-center"
                                                style="gap:6px;">
                                                <i class="fas fa-upload text-secondary"></i>
                                                <span>File Import (.xlsx / .csv)</span>
                                            </label>
                                            <div class="custom-file">
                                                <input type="file" name="file" id="file_import" accept=".xlsx,.csv" required
                                                    class="custom-file-input">
                                                <label class="custom-file-label" for="file_import">Pilih file...</label>
                                            </div>
                                            <small class="form-text text-muted mt-1">Maksimalkan akurasi dengan memakai
                                                template terbaru.</small>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-file-import"></i> Import Excel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <table id="asetTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Aset</th>
                        <th>Sub Klasifikasi Aset</th>
                        <th>Pemilik Risiko</th>
                        <th>Nilai Aset (CIA)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($asets as $aset)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $aset->nama_aset }}
                                <p class="small" style="margin-bottom: 0">{{ $aset->keterangan }}</p>
                            </td>
                            <td>{{ optional($aset->subklasifikasiaset)->subklasifikasiaset ?? '-' }}</td>
                            <td>{{ $aset->opd->namaopd ?? '-' }}</td>
                            <td style="background-color: {{ $aset->warna_hexa }}; color: #fff; font-weight: bold;">
                                {{ $aset->nilai_akhir_aset }}
                            </td>
                            <td class="actions-cell align-middle text-nowrap">
                                <a href="{{ route('opd.aset.pdf', ['aset' => $aset->uuid]) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-file-pdf"></i>
                                </a>

                                @if (($kunci ?? null) !== 'locked')
                                    <a href="{{ route('opd.aset.edit', ['aset' => $aset->uuid]) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('opd.aset.destroy', ['aset' => $aset->uuid]) }}"
                                        method="POST" style="display:inline-block">
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
                            <td colspan="6" class="text-center">Belum ada data aset untuk klasifikasi ini.</td>
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
                stateSave: true,
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

            // Tampilkan nama file di custom file input modal import
            $('#file_import').on('change', function() {
                var fileName = this.files && this.files.length ? this.files[0].name : 'Pilih file...';
                $(this).next('.custom-file-label').text(fileName);
            });
        });
    </script>
@endsection
