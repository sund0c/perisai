@extends('adminlte::page')

@section('title', 'Asets')
@section('title', 'Rekap Aset per Klasifikasi')
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
</style>
@section('content_header')
    <h1>Aset Pemprov Bali</h1>
    <div style="line-height:1.2; font-size: 0.9em">
        Aset dalam PERISAI adalah <strong>ASET INFORMASI yang mendukung kinerja organisasi dalam menjalakan proses
            bisnis/layanannya.</strong>
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
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-3" style="gap: 10px;">
                        <a href="{{ route('bidang.aset.export_rekap') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                    {{-- <table id="asetklasTable" class="table table-bordered table-hover text-center" --}}
                    <table class="table table-bordered text-center" style="width:100%;">
                        <thead>
                            <tr>
                                <th rowspan="2">Klasifikasi Aset</th>
                                <th style="width:10%" rowspan="2">Jumlah Aset</th>
                                <th colspan="3">Nilai Aset</th>
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
                                        <a href="{{ route('bidang.aset.show_by_klasifikasi', $klasifikasi->id) }}">
                                            [{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }}
                                        </a>
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
                                    <td colspan="5">
                                        Belum ada klasifikasi aset</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td>Total</td>
                                <td>{{ $klasifikasis->sum('jumlah_aset') }}</td>
                                <td style="background-color: #FF0000; color: white;">
                                    {{ $klasifikasis->sum('jumlah_tinggi') }}</td>
                                <td style="background-color: #FFD700; color: black;">
                                    {{ $klasifikasis->sum('jumlah_sedang') }}</td>
                                <td style="background-color: #00B050; color: white;">
                                    {{ $klasifikasis->sum('jumlah_rendah') }}</td>
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
                        $tTinggi = (int) ($totalTinggi ?? 0);
                        $tSedang = (int) ($totalSedang ?? 0);
                        $tRendah = (int) ($totalRendah ?? 0);
                    @endphp
                    <div style="width: 100%;margin: auto;">
                        <canvas id="pieChart" data-tinggi="{{ $tTinggi }}" data-sedang="{{ $tSedang }}"
                            data-rendah="{{ $tRendah }}"></canvas>
                    </div>
                    <br>

                    <div class="matik-list">
                        <b>Keterangan Nilai Aset (CIAAN): </b>
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
@endsection

{{-- @section('js')
    <script>
        $(function() {
            $('#asetklasTable').DataTable({
                autoWidth: false,
                columnDefs: [{
                        width: "auto",
                        targets: 0
                    },
                    {
                        width: "100px",
                        targets: 1
                    },
                    {
                        width: "auto",
                        targets: 2
                    },

                ]
            });
        });
    </script>
@endsection --}}

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        const pieData = {
            labels: ['TINGGI', 'SEDANG', 'RENDAH'],
            datasets: [{
                data: [{{ $totalTinggi }}, {{ $totalSedang }}, {{ $totalRendah }}],
                backgroundColor: ['#dc3545', '#ffc107', '#28a745'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        };
        const pieOptions = {
            responsive: true,
            maintainAspectRatio: true, // biarkan Chart.js atur proporsional

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
                        size: 16
                    },
                    formatter: (value, context) => {
                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const label = context.chart.data.labels[context.dataIndex];
                        const percentage = total ? Math.round((value / total) * 100) : 0;
                        return `${label}\n${value} (${percentage}%)`;
                    }

                }
            }
        };

        new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: pieData,
            options: pieOptions,
            plugins: [ChartDataLabels]
        });
    </script>
@endpush
