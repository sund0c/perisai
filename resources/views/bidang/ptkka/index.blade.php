@extends('adminlte::page')

@section('title', 'PTKKA')

@section('content_header')
    <h1>Penilaian Tingkat Kepatuhan Keamanan Aplikasi</h1>
    <p class="text-muted small mb-0">
        Kepgub Bali No. 584/03-E/HK/2024 Pedoman Manajemen Keamanan Informasi
    </p>
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
    <div class="row">
        {{-- KIRI: Pengajuan (status 1) --}}
        <div class="col-md-6">
            <div class="card border">
                <div class="card-header bg-light"><strong>Pengajuan PTKKA</strong></div>
                <div class="card-body">
                    <a href="{{ route('bidang.ptkka.pengajuanPDF') }}" class="btn btn-danger btn-sm mb-2">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>

                    <table id="ptkkaTablePengajuan" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Aset</th>
                                <th width="140">Pengajuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($asetsPengajuan as $aset)
                                @php($s = $aset->ptkkaPengajuan ?? null)
                                <tr>
                                    <td>

                                        {{ $aset->kode_aset ?? '-' }}
                                        <div class="small">{{ $aset->opd->namaopd ?? '-' }}</div>
                                        {{ $aset->nama_aset ?? '-' }}

                                        @if ($s)
                                            <form
                                                action="{{ route('bidang.ptkka.ajukanverifikasi', ['session' => $s->ptkka_sessions_id]) }}"
                                                method="POST" class="mt-2"
                                                onsubmit="return confirm('Apakah Anda yakin ingin melakukan verifikasi untuk aset ini?');">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    Verif Sekarang
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="small d-block mb-1">
                                            {{ optional($s?->created_at)->format('d/m/Y H:i') ?? '-' }}
                                        </span>

                                        @if (!$s || ($s->jawabans->isEmpty() ?? true))
                                            <span class="text-muted">BELUM PERNAH</span>
                                        @else
                                            @php($badge = $badgeByKat[$s->kategori_kepatuhan] ?? 'secondary')
                                            <div class="badge badge-{{ $badge }} p-2">
                                                {{ $s->kategori_kepatuhan }} {{ $s->persentase }}%
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada aset</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TENGAH: Selain 1 dan 4 → status 0/2/3 --}}
        <div class="col-md-6">
            <div class="card border">
                <div class="card-header bg-light"><strong>PTKKA On Progress</strong></div>
                <div class="card-body">
                    <a href="{{ route('bidang.ptkka.progressPDF') }}" class="btn btn-danger btn-sm mb-2">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>

                    <table id="ptkkaTableProses" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Aset</th>
                                <th width="140">Kepatuhan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($asetsProses as $aset)
                                @php($s = $aset->ptkkaTerakhir ?? null)
                                <tr>
                                    <td>
                                        <a href="{{ route('bidang.ptkka.detail', $s->id) }}"
                                            class="text-primary font-weight-bold">
                                            {{ $aset->kode_aset ?? '-' }}
                                        </a>
                                        <div class="small">{{ $aset->opd->namaopd ?? '-' }}</div>
                                        {{ $aset->nama_aset ?? '-' }}
                                    </td>
                                    <td>
                                        <span class="small d-block mb-1">
                                            {{ optional($s?->updated_at)->format('d/m/Y H:i') ?? '-' }}
                                        </span>

                                        @if (!$s || ($s->jawabans->isEmpty() ?? true))
                                            <span class="text-muted">BELUM PERNAH</span>
                                        @else
                                            @php($badge = $badgeByKat[$s->kategori_kepatuhan] ?? 'secondary')
                                            <div class="badge badge-{{ $badge }} p-2">
                                                {{ $s->kategori_kepatuhan }} {{ $s->persentase }}%
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada aset</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card border">
                <div class="card-header bg-light"><strong>Aset TIK Pemprov Bali</strong></div>
                <div class="card-body">
                    <a href="{{ route('bidang.ptkka.closingPDF') }}" class="btn btn-danger btn-sm mb-2">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>

                    <table id="ptkkaTableRampung" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="50">KODE</th>
                                <th>NAMA ASET</th>
                                <th width="60">STANDAR</th>
                                <th width="130">PEMILIK ASET</th>
                                <th width="120">TGL PTKKA</th>
                                <th width="100">KEPATUHAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($asetsRampung as $aset)
                                @php($s = $aset->ptkkaTerakhirRampung ?? null)
                                <tr>
                                    <td>

                                        {{ $aset->kode_aset ?? '-' }}

                                    </td>
                                    <td>
                                        {{ $aset->nama_aset ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $aset->kategori_label_terakhir }}
                                    </td>
                                    <td>
                                        {{ $aset->opd->namaopd ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $s?->updated_at ? $s->updated_at->translatedFormat('j F Y') : '-' }}
                                    </td>
                                    <td>
                                        @if (!$s || ($s->jawabans->isEmpty() ?? true))
                                            <span class="text-muted">BELUM PERNAH</span>
                                        @else
                                            @php($badge = $badgeByKat[$s->kategori_kepatuhan] ?? 'secondary')
                                            <a href="{{ route('bidang.ptkka.exportPDF', $s->id) }}" target="_blank"
                                                title="Export PDF" class="badge badge-{{ $badge }} p-2"
                                                style="text-decoration: none;">
                                                {{ $s->kategori_kepatuhan }} {{ $s->persentase }}%
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada aset</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <script>
        $(function() {
            const dtOptionsDefault = {
                autoWidth: false,
                pageLength: 25,
                lengthChange: false,
                searching: false,
                language: {
                    info: "_START_/_END_ data",
                    infoEmpty: "0/0 data",
                    infoFiltered: ""
                },
                dom: '<"top"f>rtip',
            };

            const dtOptionsRampung = {
                autoWidth: false,
                pageLength: 25,
                lengthChange: true, // jumlah data per halaman ditampilkan
                searching: true, // aktifkan search box
                language: {
                    info: "_START_/_END_ data",
                    infoEmpty: "0/0 data",
                    infoFiltered: ""
                },
            };
            $('#ptkkaTableRampung').DataTable(dtOptionsRampung);
            $('#ptkkaTablePengajuan').DataTable(dtOptionsDefault);
            $('#ptkkaTableProses').DataTable(dtOptionsDefault);

        });
    </script>
@endsection
