@extends('adminlte::page')

@section('title', 'Vitalitas SE ' . strtoupper($kategori))

@section('content_header')
    @php
        $kategori = strtolower($kategori);

        $label =
            [
                'vital' => 'Vital',
                'novital' => 'Tidak Vital',
                'belum' => 'Belum Dinilai',
            ][$kategori] ?? 'Tidak Diketahui';
    @endphp
    <h1>SE Status Vitalitas : {{ $label }} di Pemprov Bali</h1>
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
        <div class="card-body table-responsive">
            <div class="d-flex mb-3" style="gap: 10px;">
                <a href="{{ route('bidang.vitalitasse.index') }}" class="btn btn-secondary mb-3 me-2">
                    ← Kembali
                </a>
                <a href="{{ route('bidang.vitalitasse.export_rekap_kategori', ['kategori' => $kategori]) }}"
                    class="btn btn-danger mb-3">

                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
            <table id="kategoriseTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">

                <thead>
                    <tr>
                        <th>Nama Aset</th>
                        <th>Sub Klasifikasi</th>
                        <th>Lokasi</th>
                        <th>Vitalitas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $aset)
                        <tr>
                            <td>{{ $aset->nama_aset }}</td>
                            <td>{{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</td>
                            <td>{{ $aset->lokasi }}</td>
                            <td>
                                {{-- {{ $aset->vitalitasSe->skor_total ?? 'BELUM DINILAI' }} --}}
                                @php
                                    $skor = $aset->vitalitasSe->skor_total ?? null;

                                    if (is_null($skor)) {
                                        $label = 'BELUM DINILAI';
                                        $warna = '#6c757d'; // abu
                                        $warnaTeks = '#fff';
                                    } elseif ($skor >= 15) {
                                        $label = 'VITAL';
                                        $warna = '#dc3545'; // merah
                                        $warnaTeks = '#fff';
                                    } else {
                                        $label = 'Tidak Vital';
                                        $warna = '#28a745'; // hijau
                                        $warnaTeks = '#fff';
                                    }
                                @endphp

                                <span class="badge"
                                    style="background-color: {{ $warna }}; color: {{ $warnaTeks }};">
                                    {{ $skor }} ({{ $label }})
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('bidang.vitalitasse.exportPdf', $aset->id) }}"
                                    class="btn btn-sm btn-primary me-1">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                @if ($aset->vitalitasSe)
                                    <form action="{{ route('bidang.vitalitasse.destroy', $aset->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus vitalitas SE aset ini?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
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
                columnDefs: [{
                        width: "auto",
                        targets: 0
                    },
                    {
                        width: "200px",
                        targets: 1
                    },
                    {
                        width: "200px",
                        targets: 2
                    },
                    {
                        width: "100px",
                        targets: 3
                    },
                    {
                        width: "100px",
                        targets: 4
                    }
                ]
            });
        });
    </script>
@endsection
