@extends('adminlte::page')

@section('title', 'Kategori SE ' . strtoupper($kategori))

@section('content_header')
    <h1>SE Kategori {{ strtoupper($kategori) }} di Pemprov Bali</h1>
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
    <div class="card">
        <div class="card-body table-responsive">
            <div class="d-flex mb-3" style="gap: 10px;">
                <a href="{{ route('bidang.kategorise.index') }}" class="btn btn-secondary mb-3 me-2 btn-sm">
                    ← Kembali
                </a>
                <a href="{{ route('bidang.kategorise.export_rekap_kategori', ['kategori' => $kategori]) }}"
                    class="btn btn-danger mb-3 btn-sm">

                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
            <table id="kategoriseTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">

                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Aset</th>
                        <th>Sub Klasifikasi</th>
                        <th>Pemilik Aset</th>
                        <th>Kategorisasi</th>
                        <th>Detil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $aset)
                        <tr>
                            <td>{{ $aset->kode_aset }}</td>
                            <td>{{ $aset->nama_aset }}</td>
                            <td>{{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</td>
                            <td>{{ $aset->opd->namaopd ?? '-' }}</td>
                            <td align="center">
                                {{-- {{ $aset->kategoriSe->skor_total ?? 'BELUM DINILAI' }} --}}
                                @php
                                    $skor = $aset->kategoriSe->skor_total ?? null;

                                    if ($skor === null) {
                                        echo '<span class="badge badge-secondary">BELUM DINILAI</span>';
                                    } else {
                                        $range = $rangeSes->first(function ($r) use ($skor) {
                                            return $skor >= $r->nilai_bawah && $skor <= $r->nilai_atas;
                                        });

                                        $warna = $range->warna_hexa ?? '#999';
                                        $label = $range->nilai_akhir_aset ?? 'TIDAK DIKETAHUI';

                                        echo '<span class="badge" style="background-color: ' .
                                            $warna .
                                            '; color: #fff;">' .
                                            $skor .
                                            ' (' .
                                            strtoupper($label) .
                                            ')</span>';
                                    }
                                @endphp
                            </td>

                            <td align="center">
                                <a href="{{ route('bidang.kategorise.exportPdf', $aset->id) }}"
                                    class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
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
                        width: "50",
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
                        width: "150px",
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
