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

    /* Perbesar checkbox bulk delete agar mudah diklik */
    .bulk-check {
        width: 18px;
        height: 18px;
        cursor: pointer;
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
                <a href="{{ route('opd.aset.index') }}" class="btn btn-sm btn-secondary mb-3 me-2 hide-when-select">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>

                {{-- WAJIB: kirim param "klasifikasiaset" --}}
                <a href="{{ route('opd.aset.export_rekap_klas', ['klasifikasiaset' => $klasifikasi]) }}"
                    class="btn btn-sm btn-danger mb-3 hide-when-select" target="_blank">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>

                <button type="button" class="btn btn-sm btn-outline-success mb-3 hide-when-select" data-toggle="modal"
                    data-target="#excelModal">
                    <i class="fas fa-file-excel"></i> Import
                </button>

                @if (($kunci ?? null) !== 'locked')
                    <a href="{{ route('opd.aset.create', ['klasifikasiaset' => $klasifikasi]) }}"
                        class="btn btn-sm btn-success mb-3 hide-when-select">
                        <i class="fas fa-plus"></i> Tambah Aset
                    </a>
                    <button type="button" class="btn btn-sm btn-danger mb-3 d-none" id="bulkDeleteBtn">
                        <i class="fas fa-trash"></i> Hapus Terpilih
                    </button>
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
                                <small class="text-muted mb-2">Unduh template untuk referensi kolom yang benar sebelum
                                    mengisi data.</small>
                                <a href="{{ route('opd.aset.export_excel', ['klasifikasiaset' => $klasifikasi]) }}"
                                    class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                                <small class="text-muted mb-2">Export data aset saat ini sebagai contoh atau arsip.</small>
                                @if (($kunci ?? null) !== 'locked')
                                    <form
                                        action="{{ route('opd.aset.import_excel', ['klasifikasiaset' => $klasifikasi]) }}"
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
                                                <input type="file" name="file" id="file_import" accept=".xlsx,.csv"
                                                    required class="custom-file-input">
                                                <label class="custom-file-label" for="file_import">Pilih file...</label>
                                            </div>
                                            <small class="form-text text-muted mt-1">Gunakan template terbaru, isi sesuai
                                                dropdown yang tersedia, lalu unggah di sini.</small>
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

            {{-- @php
                function badgeCIA($nilai)
                {
                    $label = ['1' => 'R', '2' => 'S', '3' => 'T'][$nilai] ?? '-';
                    $warna =
                        [
                            '1' => 'background-color:#28a745;color:#fff;', // Hijau
                            '2' => 'background-color:#ffc107;color:#000;', // Kuning
                            '3' => 'background-color:#dc3545;color:#fff;', // Merah
                        ][$nilai] ?? 'background-color:#ccc;color:#000;';

                    return '<span style="padding:3px 8px; border-radius:4px; font-weight:bold; ' .
                        $warna .
                        '">' .
                        $label .
                        '</span>';
                }
            @endphp --}}

            <table id="asetTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">
                <thead>
                    <tr>
                        @if (($kunci ?? null) !== 'locked')
                            <th style="width:35px;" class="text-center align-middle">
                                <input type="checkbox" id="checkAll" class="bulk-check">
                            </th>
                        @endif
                        <th>#</th>
                        <th>Nama Aset</th>
                        <th>Sub Klasifikasi</th>
                        <th>Lokasi</th>
                        <th>C</th>
                        <th>I</th>
                        <th>A</th>
                        <th>Kritikalitas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @forelse ($asets as $aset)
                        <tr>
                            @if (($kunci ?? null) !== 'locked')
                                <td class="text-center align-middle">
                                    <input type="checkbox" name="aset_ids[]" value="{{ $aset->id }}"
                                        class="row-check bulk-check">
                                </td>
                            @endif
                            <td>{{ $no++ }}</td>
                            <td>{{ $aset->nama_aset }}
                                <p class="small" style="margin-bottom: 0">{{ $aset->keterangan }}</p>
                            </td>
                            <td>{{ optional($aset->subklasifikasiaset)->subklasifikasiaset ?? '-' }}
                                <p class="small" style="margin-bottom: 0">{{ $aset->spesifikasi_aset }}</p>
                            </td>
                            <td>{{ $aset->lokasi ?? '-' }}
                                <p class="small" style="margin-bottom: 0">{{ $aset->format_penyimpanan }}</p>
                                <p class="small" style="margin-bottom: 0">{{ $aset->link_url }}</p>
                            </td>
                            <td>{!! $badges[$aset->id]['c'] !!}</td>
                            <td>{!! $badges[$aset->id]['i'] !!}</td>
                            <td>{!! $badges[$aset->id]['a'] !!}</td>


                            <td style="background-color: {{ $aset->warna_hexa }}; color: #fff; font-weight: bold;">
                                {{ $aset->nilai_akhir_aset }}
                            </td>
                            <td class="actions-cell align-middle text-nowrap">
                                <a href="{{ route('opd.aset.pdf', ['aset' => $aset->uuid]) }}"
                                    class="btn btn-sm btn-success" target="_blank">
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
                            <td colspan="{{ ($kunci ?? null) === 'locked' ? 6 : 7 }}" class="text-center">Belum ada data
                                aset untuk klasifikasi ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if (($kunci ?? null) !== 'locked')
                <form id="bulkDeleteForm"
                    action="{{ route('opd.aset.bulk_destroy', ['klasifikasiaset' => $klasifikasi]) }}" method="POST"
                    class="d-none">
                    @csrf
                </form>
            @endif

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
            var isLocked = @json(($kunci ?? null) === 'locked');

            $('#asetTable').DataTable({
                autoWidth: false,
                stateSave: true,
                columnDefs: isLocked ? [{
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
                        width: "50px",
                        targets: 4
                    },
                    {
                        width: "50px",
                        targets: 5
                    },
                    {
                        width: "50px",
                        targets: 6
                    },
                    {
                        width: "100px",
                        targets: 7
                    },
                    {
                        width: "140px",
                        targets: 8
                    },
                ] : [{
                        width: "40px",
                        targets: 0
                    },
                    {
                        width: "10px",
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
                        width: "auto",
                        targets: 4
                    },
                    {
                        width: "20px",
                        targets: 5
                    },
                    {
                        width: "20px",
                        targets: 6
                    },
                    {
                        width: "20px",
                        targets: 7
                    },
                    {
                        width: "90px",
                        targets: 8
                    },
                    {
                        width: "90px",
                        targets: 9
                    },
                ]
            });

            $('#file_import').on('change', function() {
                var fileName = this.files && this.files.length ? this.files[0].name : 'Pilih file...';
                $(this).next('.custom-file-label').text(fileName);
            });

            if (!isLocked) {
                $('#checkAll').on('change', function() {
                    $('.row-check').prop('checked', $(this).is(':checked'));
                    toggleBulkDeleteBtn();
                    toggleOtherButtons();
                });
                $('.row-check').on('change', function() {
                    if (!$(this).is(':checked')) {
                        $('#checkAll').prop('checked', false);
                    }
                    toggleBulkDeleteBtn();
                    toggleOtherButtons();
                });

                $('#bulkDeleteBtn').on('click', function(e) {
                    e.preventDefault();
                    const checked = $('.row-check:checked');
                    if (checked.length === 0) {
                        alert('Pilih minimal satu aset untuk dihapus.');
                        return;
                    }
                    if (!confirm('Yakin hapus aset terpilih?')) {
                        return;
                    }

                    const form = $('#bulkDeleteForm');
                    form.find('input[name="aset_ids[]"]').remove();
                    checked.each(function() {
                        $('<input>', {
                            type: 'hidden',
                            name: 'aset_ids[]',
                            value: $(this).val()
                        }).appendTo(form);
                    });
                    form.submit();
                });

                function toggleBulkDeleteBtn() {
                    const hasSelection = $('.row-check:checked').length > 0;
                    $('#bulkDeleteBtn').toggleClass('d-none', !hasSelection);
                }

                function toggleOtherButtons() {
                    const hasSelection = $('.row-check:checked').length > 0;
                    $('.hide-when-select').toggleClass('d-none', hasSelection);
                }

                toggleOtherButtons();
            }
        });
    </script>
@endsection
