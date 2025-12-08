@extends('adminlte::page')

@section('title', 'Aset Informasi')
<style>
    .chart-donut-wrapper {
        position: relative;
        width: 100%;
        max-width: 280px;
        /* bebas, yang penting sama untuk semua */
        height: 280px;
        /* tinggi sama untuk semua donut */
        margin: 0 auto;
        /* biar donut di tengah card */
    }

    .chart-donut-wrapper canvas {
        width: 100% !important;
        height: 100% !important;
    }


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
</style>
@section('content_header')
    <h1>Aset</h1>
    <div style="line-height:1.2; font-size: 0.9em">
        Aset dalam PERISAI adalah ASET INFORMASI yang mendukung kinerja organisasi dalam menjalankan proses
        bisnis/layanannya.
    </div>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif {{ $tahunAktifGlobal ?? '-' }}
            @if (($kunci ?? null) === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @endif
            :: {{ strtoupper($namaOpd ?? '-') }}
        </span>
    </li>
@endsection


@section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><b>STATUS KRITIKAL & JUMLAH ASET per KLASIFIKASI</b></h5>
                </div>
                <div class="card-body text-center">

                    <div class="row">
                        @php
                            $tTinggi = $klasifikasis->sum('jumlah_tinggi') ?? 0;
                            $tSedang = $klasifikasis->sum('jumlah_sedang') ?? 0;
                            $tRendah = $klasifikasis->sum('jumlah_rendah') ?? 0;
                        @endphp
                        {{-- Kiri: Donut --}}
                        <div class="col-md-6 d-flex justify-content-center">
                            {{-- <div class="chart-box w-100" style="max-width:260px; height:260px;"> --}}
                            <div class="chart-donut-wrapper">

                                <canvas id="pieChart1" data-tinggi="{{ $tTinggi }}" data-sedang="{{ $tSedang }}"
                                    data-rendah="{{ $tRendah }}">
                                </canvas>
                            </div>
                        </div>

                        {{-- Kanan: Bar --}}
                        <div class="col-md-6 d-flex justify-content-center">
                            <div class="chart-box w-100" style="max-width:320px; height:260px;">
                                <canvas id="barChart1"></canvas>
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div>
                        <div class="d-flex mb-3" style="gap: 10px;">
                            <a href="{{ route('opd.aset.export_rekap') }}" class="btn btn-light btn-sm mb-3 me-2"
                                target="_blank">
                                <i class="fas fa-file-pdf"></i> PDF Klasifikasi Aset Lengkap</a>

                            @if (($kunci ?? null) !== 'locked')
                                @if (!empty($canSync) && $canSync)
                                    <form action="{{ route('opd.kategorise.sync_previous') }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm mb-3 me-2"
                                            onclick="return confirm('Sinkronisasi akan menyalin semua aset dari tahun sebelumnya ke periode aktif. Lanjutkan?')">
                                            <i class="fas fa-sync-alt"></i> Sinkron Tahun Sebelumnya
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>

                        <table class="table table-bordered text-center" style="width:100%;">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="vertical-align: middle; text-align: center;">Jml Aset per
                                        Klasifikasi</th>
                                    <th style="width:15%;vertical-align: middle; text-align: center;" rowspan="2">Jml
                                    </th>
                                    <th colspan="3">Kritikalitas</th>
                                </tr>
                                <tr>
                                    <th style="background-color: #FF0000; color: white;width:15%">T</th>
                                    <th style="background-color: #FFD700; color: black;width:15%">S</th>
                                    <th style="background-color: #00B050; color: white;width:15%">R</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($klasifikasis as $klasifikasi)
                                    <tr>
                                        <td style="text-align: left;">
                                            <a class="btn btn-primary btn-sm px-4 w-100"
                                                href="{{ route('opd.aset.show_by_klasifikasi', ['klasifikasiaset' => $klasifikasi]) }}">

                                                [{{ $klasifikasi->kodeklas }}]
                                                {{ $klasifikasi->klasifikasaset ?? $klasifikasi->klasifikasiaset }}</a>
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            {{ $klasifikasi->jumlah_aset ?? 0 }}
                                        </td>
                                        <td
                                            style="background-color:#FF0000;color:#fff;vertical-align:middle;text-align:center;">
                                            {{ $klasifikasi->jumlah_tinggi ?? 0 }}
                                        </td>
                                        <td
                                            style="background-color:#FFD700;color:#000;vertical-align:middle;text-align:center;">
                                            {{ $klasifikasi->jumlah_sedang ?? 0 }}
                                        </td>
                                        <td
                                            style="background-color:#00B050;color:#fff;vertical-align:middle;text-align:center;">
                                            {{ $klasifikasi->jumlah_rendah ?? 0 }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Belum ada klasifikasi aset</td>
                                    </tr>
                                @endforelse
                            </tbody>

                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td style="vertical-align: middle; text-align: center;">Total</td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        {{ $klasifikasis->sum('jumlah_aset') }}
                                    </td>
                                    <td
                                        style="background-color: #FF0000; color: white;vertical-align: middle; text-align: center;">
                                        {{ $klasifikasis->sum('jumlah_tinggi') }}
                                    </td>
                                    <td
                                        style="background-color: #FFD700; color: black;vertical-align: middle; text-align: center;">
                                        {{ $klasifikasis->sum('jumlah_sedang') }}
                                    </td>
                                    <td
                                        style="background-color: #00B050; color: white;vertical-align: middle; text-align: center;">
                                        {{ $klasifikasis->sum('jumlah_rendah') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>

            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><b>KATEGORI SE</b></h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        {{-- <div style="width: 100%;margin: auto;"> --}}
                        <div class="chart-donut-wrapper">
                            <canvas id="pieChart2"></canvas>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div>
                        <div class="d-flex mb-3" style="gap: 10px;">
                            <a href="{{ route('opd.kategorise.export_rekap') }}" class="btn btn-light btn-sm"
                                target="_blank">

                                <i class="fas fa-file-pdf"></i> PDF Kategorisasi SE Lengkap</a>
                            </a>
                        </div>
                        <table class="table table-bordered text-center" style="width:100%">
                            <thead class="font-weight-bold">
                                <tr>
                                    <th style="width:80%">Kategori SE</th>
                                    <th>Jml</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="text-align: left">
                                    <td style="text-align: left">
                                        <a class="btn btn-danger btn-sm px-4 w-100"
                                            href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'strategis']) }}">
                                            STRATEGIS
                                        </a>
                                    </td>
                                    <td>
                                        <a class="btn btn-danger btn-sm px-4 w-100"
                                            href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'strategis']) }}">{{ $strategis }}</a>
                                    </td>
                                </tr>
                                <tr style="text-align: left">
                                    <td style="text-align: left">
                                        <a class="btn btn-warning btn-sm px-4 w-100"
                                            href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'tinggi']) }}">
                                            TINGGI
                                        </a>
                                    </td>
                                    <td>
                                        <a class="btn btn-warning btn-sm px-4 w-100"
                                            href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'tinggi']) }}">
                                            {{ $tinggi }}</a>
                                    </td>
                                </tr>
                                <tr style="text-align: left">
                                    <td style="text-align: left"><a class="btn btn-success btn-sm px-4 w-100"
                                            href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'rendah']) }}">
                                            RENDAH
                                        </a>

                                    </td>
                                    <td>
                                        <a class="btn btn-success btn-sm px-4 w-100"
                                            href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'rendah']) }}">{{ $rendah }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: left">
                                        <a class="btn btn-secondary btn-sm px-4 w-100"
                                            href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'belum']) }}">
                                            (belum dinilai)</a>
                                    </td>
                                    <td>
                                        <a class="btn btn-secondary btn-sm px-4 w-100"
                                            href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'belum']) }}">{{ $belum }}</a>
                                    </td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td>Total Jumlah SE</td>
                                    <td>{{ $total }}</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><b>STATUS VITALITAS SE</b></h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        {{-- <div style="width: 100%;margin: auto;"> --}}
                        <div class="chart-donut-wrapper">
                            <canvas id="pieChart3"></canvas>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div>
                        <div class="d-flex mb-3" style="gap: 10px;">
                            <a href="{{ route('opd.vitalitasse.export_rekap') }}" class="btn btn-light btn-sm"
                                target="_blank">

                                <i class="fas fa-file-pdf"></i> Export PDF Status Vital Lengkap
                            </a>
                        </div>
                        <table class="table table-bordered text-center" style="width:100%">
                            <thead class="font-weight-bold">
                                <tr>
                                    <th style="width:80%">Status Vital SE</th>
                                    <th>Jml</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <a class="btn btn-danger btn-sm px-4 w-100"
                                            href="{{ route('opd.vitalitasse.show_by_kategori', ['kategori' => 'vital']) }}">
                                            Aset VITAL
                                        </a>
                                    </td>


                                    <td style="vertical-align: middle; text-align: center;">
                                        <a class="btn btn-danger btn-sm px-4 w-100"
                                            href="{{ route('opd.vitalitasse.show_by_kategori', ['kategori' => 'vital']) }}">
                                            {{ $vital1 }}
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td><a class="btn btn-success btn-sm px-4 w-100"
                                            href="{{ route('opd.vitalitasse.show_by_kategori', ['kategori' => 'novital']) }}">Aset
                                            Non Vital</a>
                                    </td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        <a class="btn btn-success btn-sm px-4 w-100"
                                            href="{{ route('opd.vitalitasse.show_by_kategori', ['kategori' => 'novital']) }}">
                                            {{ $novital1 }}
                                        </a>
                                    </td>
                                </tr>

                                <tr>
                                    <td><a class="btn btn-secondary btn-sm px-4 w-100"
                                            href="{{ route('opd.vitalitasse.show_by_kategori', ['kategori' => 'belum']) }}">
                                            (belum dinilai)</a>
                                    </td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        <a class="btn btn-secondary btn-sm px-4 w-100"
                                            href="{{ route('opd.vitalitasse.show_by_kategori', ['kategori' => 'belum']) }}">
                                            {{ $belum1 }}
                                        </a>
                                    </td>
                                </tr>

                                <tr class="font-weight-bold">
                                    <td>Total Jumlah SE</td>
                                    <td>{{ $total1 }}</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>
    </br>


@endsection


{{-- @section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div style="gap: 10px;">
                        <a href="{{ route('opd.aset.export_rekap') }}" class="btn btn-danger btn-sm mb-3 me-2"
                            target="_blank">
                            <i class="fas fa-file-pdf"></i> Export PDF Lengkap</a>

                        @if (($kunci ?? null) !== 'locked')
                            @if (!empty($canSync) && $canSync)
                                <form action="{{ route('opd.kategorise.sync_previous') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm mb-3 me-2"
                                        onclick="return confirm('Sinkronisasi akan menyalin semua aset dari tahun sebelumnya ke periode aktif. Lanjutkan?')">
                                        <i class="fas fa-sync-alt"></i> Sinkron Tahun Sebelumnya
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>

                    <table class="table table-bordered text-center" style="width:100%;">
                        <thead>
                            <tr>
                                <th rowspan="2" style="vertical-align: middle; text-align: center;">Klasifikasi Aset</th>
                                <th style="width:20%;vertical-align: middle; text-align: center;" rowspan="2">Jumlah Aset
                                </th>
                                <th colspan="3">Nilai Kritikalitas Aset</th>
                            </tr>
                            <tr>
                                <th style="background-color: #FF0000; color: white;width:10%">TINGGI</th>
                                <th style="background-color: #FFD700; color: black;width:10%">SEDANG</th>
                                <th style="background-color: #00B050; color: white;width:10%">RENDAH</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($klasifikasis as $klasifikasi)
                                <tr>
                                    <td style="text-align: left;">
                                         <a
                                            href="{{ route('opd.aset.show_by_klasifikasi', ['klasifikasiaset' => $klasifikasi]) }}">
                                            [{{ $klasifikasi->kodeklas }}]
                                            {{ $klasifikasi->klasifikasaset ?? $klasifikasi->klasifikasiaset }}</a>
                                    </td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        {{ $klasifikasi->jumlah_aset ?? 0 }}
                                    </td>
                                    <td
                                        style="background-color:#FF0000;color:#fff;vertical-align:middle;text-align:center;">
                                        {{ $klasifikasi->jumlah_tinggi ?? 0 }}
                                    </td>
                                    <td
                                        style="background-color:#FFD700;color:#000;vertical-align:middle;text-align:center;">
                                        {{ $klasifikasi->jumlah_sedang ?? 0 }}
                                    </td>
                                    <td
                                        style="background-color:#00B050;color:#fff;vertical-align:middle;text-align:center;">
                                        {{ $klasifikasi->jumlah_rendah ?? 0 }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Belum ada klasifikasi aset</td>
                                </tr>
                            @endforelse
                        </tbody>

                        <tfoot>
                            <tr class="font-weight-bold">
                                <td style="vertical-align: middle; text-align: center;">Total</td>
                                <td style="vertical-align: middle; text-align: center;">
                                    {{ $klasifikasis->sum('jumlah_aset') }}
                                </td>
                                <td
                                    style="background-color: #FF0000; color: white;vertical-align: middle; text-align: center;">
                                    {{ $klasifikasis->sum('jumlah_tinggi') }}
                                </td>
                                <td
                                    style="background-color: #FFD700; color: black;vertical-align: middle; text-align: center;">
                                    {{ $klasifikasis->sum('jumlah_sedang') }}
                                </td>
                                <td
                                    style="background-color: #00B050; color: white;vertical-align: middle; text-align: center;">
                                    {{ $klasifikasis->sum('jumlah_rendah') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <br>
                    <b>Keterangan Klasifikasi Aset :</b>
                    <div class="matik-list">
                        <ul>
                            @foreach ($klasifikasis as $klasifikasi)
                                <li><b>[{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }}</b>
                                    <ul>
                                        @foreach ($klasifikasi->subklasifikasi as $sub)
                                            <li>{{ $sub->subklasifikasiaset }} : {{ $sub->penjelasan }}</li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    @php
                        // $tTinggi = (int) ($totalTinggi ?? 0);
                        // $tSedang = (int) ($totalSedang ?? 0);
                        // $tRendah = (int) ($totalRendah ?? 0);
                        $tTinggi = $klasifikasis->sum('jumlah_tinggi') ?? 0;
                        $tSedang = $klasifikasis->sum('jumlah_sedang') ?? 0;
                        $tRendah = $klasifikasis->sum('jumlah_rendah') ?? 0;
                    @endphp
                    <div style="width: 100%;margin: auto;">
                        <canvas id="pieChart" data-tinggi="{{ $tTinggi }}" data-sedang="{{ $tSedang }}"
                            data-rendah="{{ $tRendah }}"></canvas>
                    </div>
                    <br>

                    <div class="matik-list">
                        <b>Keterangan Kritikalitas Aset : </b>
                        <ol style="padding-left: 20px; margin-left: 0;">
                            @foreach ($ranges ?? collect() as $range)
                                <li><b>{{ $range->nilai_akhir_aset }} :</b> {{ $range->deskripsi }}</li>
                            @endforeach
                        </ol>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection --}}

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        var pieEl = document.getElementById('pieChart1');

        // Ambil data dari atribut data-*
        var tinggi = parseInt(pieEl.dataset.tinggi) || 0;
        var sedang = parseInt(pieEl.dataset.sedang) || 0;
        var rendah = parseInt(pieEl.dataset.rendah) || 0;

        const pieData1 = {
            labels: ['TINGGI', 'SEDANG', 'RENDAH'],
            datasets: [{
                data: [tinggi, sedang, rendah],
                backgroundColor: ['#dc3545', '#ffc107', '#28a745'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        };

        const pieOptions1 = {
            responsive: true,
            maintainAspectRatio: true,

            cutout: '40%', // ⬅️ menjadikannya DONUT

            plugins: {
                legend: {
                    position: 'bottom'
                },

                datalabels: {
                    display: (ctx) => ctx.dataset.data[ctx.dataIndex] !== 0,
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 14
                    },
                    formatter: (value, context) => {
                        const total = context.chart.data.datasets[0].data
                            .reduce((a, b) => a + b, 0);

                        const label = context.chart.data.labels[context.dataIndex];
                        const percentage = total ? Math.round((value / total) * 100) : 0;

                        return `${percentage}%`;
                    }
                }
            }
        };

        new Chart(pieEl, {
            type: 'doughnut', // ⬅️ Ubah dari pie → donut chart
            data: pieData1,
            options: pieOptions1,
            plugins: [ChartDataLabels]
        });
    </script>

    <script>
        const pieData2 = {
            labels: ['STRATEGIS', 'TINGGI', 'RENDAH', 'BELUM DINILAI'],
            datasets: [{
                data: [{{ $strategis }}, {{ $tinggi }}, {{ $rendah }}, {{ $belum }}],
                backgroundColor: ['#dc3545', '#ffc107', '#28a745', '#6c757d'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        };

        const pieOptions2 = {
            responsive: true,
            maintainAspectRatio: true,

            cutout: '45%', // ⬅️ INI YANG MEMBUATNYA DONUT

            plugins: {
                legend: {
                    position: 'bottom'
                },

                datalabels: {
                    display: function(context) {
                        return context.dataset.data[context.dataIndex] !== 0;
                    },
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 14
                    },
                    formatter: (value, context) => {
                        const total = context.chart.data.datasets[0].data
                            .reduce((a, b) => a + b, 0);

                        const percentage = total ? Math.round((value / total) * 100) : 0;

                        return `${percentage}%`;
                    }
                }
            }
        };

        new Chart(document.getElementById('pieChart2'), {
            type: 'doughnut', // ⬅️ Ubah menjadi donut
            data: pieData2,
            options: pieOptions2,
            plugins: [ChartDataLabels]
        });
    </script>

    <script>
        const pieData3 = {
            labels: ['VITAL', 'Non Vital', 'Belum Dinilai'],
            datasets: [{
                data: [{{ $vital1 }}, {{ $novital1 }}, {{ $belum1 }}],
                backgroundColor: ['#dc3545', '#28a745', '#6c757d'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        };

        const pieOptions3 = {
            responsive: true,
            maintainAspectRatio: true,

            cutout: '45%', // ⬅️ membuat pie menjadi DONUT

            plugins: {
                legend: {
                    position: 'bottom'
                },

                datalabels: {
                    display: function(context) {
                        return context.dataset.data[context.dataIndex] !== 0;
                    },
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 14
                    },
                    formatter: (value, context) => {
                        const total = context.chart.data.datasets[0].data
                            .reduce((a, b) => a + b, 0);

                        const percentage = total ? Math.round((value / total) * 100) : 0;
                        return `${percentage}%`;
                    }
                }
            }
        };

        new Chart(document.getElementById('pieChart3'), {
            type: 'doughnut', // ⬅️ Ubah menjadi donut
            data: pieData3,
            options: pieOptions3,
            plugins: [ChartDataLabels]
        });
    </script>
    <script>
        // ==== BAR CHART TOTAL ASET PER KLASIFIKASI ====
        const barCtx = document.getElementById('barChart1');

        const barLabels = @json($barLabels);
        const barTotals = @json($barTotals);

        // === PLUGIN UNTUK ANGKA DI ATAS BAR ===
        const valueLabelPlugin = {
            id: 'valueLabelPlugin',
            afterDatasetsDraw(chart, args, pluginOptions) {
                const {
                    ctx
                } = chart;

                chart.data.datasets.forEach((dataset, i) => {
                    chart.getDatasetMeta(i).data.forEach((bar, index) => {
                        const value = dataset.data[index];
                        if (value === null || value === undefined) return;

                        ctx.save();
                        ctx.font = 'bold 13px sans-serif';
                        ctx.fillStyle = '#000';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';

                        ctx.fillText(value, bar.x, bar.y - 6);
                        ctx.restore();
                    });
                });
            }
        };

        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{
                    label: 'Total Aset',
                    data: barTotals,
                    backgroundColor: '#007bff',
                    borderColor: '#0056b3',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    },
                },

                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            display: false
                        }, // hilangkan angka Y
                        grid: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            },
            plugins: [valueLabelPlugin] // ⛳ MASUKKAN PLUGIN DI SINI
        });
    </script>
@endpush
