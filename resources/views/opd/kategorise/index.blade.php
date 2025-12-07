@extends('adminlte::page')

@section('title', 'Kategorisasi SE')

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
    <h1>Kategorisasi SE</h1>
    <div style="line-height:1.2; font-size: 0.9em">
        Sistem Elektronik (SE) dalam PERISAI adalah ASET INFORMASI dengan klasifikasi [PL] Perangkat
        Lunak khusus Aplikasi berbasis Website, Mobile dan Desktop.
    </div>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
            @if ($kunci === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @endif
            :: {{ strtoupper($namaOpd) }}
        </span>
    </li>
@endsection



@section('content')
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-3" style="gap: 10px;">
                        <a href="{{ route('opd.kategorise.export_rekap') }}" class="btn btn-danger btn-sm" target="_blank">

                            <i class="fas fa-file-pdf"></i> Export PDF Lengkap
                        </a>
                    </div>
                    <table class="table table-bordered text-center" style="width:100%">
                        <thead class="font-weight-bold">
                            <tr>
                                <th style="width:60%">Kategori Aset</th>
                                <th>Jumlah Aset</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-danger text-white" style="text-align: left">
                                <td style="text-align: left"><b>STRATEGIS</b>
                                </td>
                                <td style="vertical-align: middle; text-align: center;">
                                    <a class="btn btn-light btn-sm px-4"
                                        href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'strategis']) }}">
                                        {{ $strategis }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="bg-warning text-white" style="text-align: left">
                                <td style="text-align: left"><b>TINGGI</b>
                                </td>
                                <td style="vertical-align: middle; text-align: center;">
                                    <a class="btn btn-light btn-sm px-4"
                                        href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'tinggi']) }}">
                                        {{ $tinggi }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="bg-success text-white" style="text-align: left">
                                <td style="text-align: left"><b>RENDAH</b>
                                </td>
                                <td style="vertical-align: middle; text-align: center;">
                                    <a class="btn btn-light btn-sm px-4"
                                        href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'rendah']) }}">
                                        {{ $rendah }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="bg-secondary text-white" style="text-align: left">
                                <td style="text-align: left"><b>Belum Dinilai</b>
                                </td>
                                <td style="vertical-align: middle; text-align: center;">
                                    <a class="btn btn-light btn-sm px-4"
                                        href="{{ route('opd.kategorise.show_by_kategori', ['kategori' => 'belum']) }}">
                                        {{ $belum }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="font-weight-bold">
                                <td>Total Jumlah Aset</td>
                                <td>{{ $total }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <b>Keterangan</b>
                    <div class="matik-list">
                        <ul>
                            <li><b>STRATEGIS: </b>{{ data_get($kategoriMeta, 'STRATEGIS.deskripsi', '-') }}</li>
                            <li><b>TINGGI: </b>{{ data_get($kategoriMeta, 'TINGGI.deskripsi', '-') }}</li>
                            <li><b>RENDAH: </b>{{ data_get($kategoriMeta, 'RENDAH.deskripsi', '-') }}</li>
                            <li><b>BELUM: </b>{{ data_get($kategoriMeta, 'BELUM.deskripsi', '-') }}</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body text-center">
                    <div style="width: 100%;margin: auto;">
                        <canvas id="pieChart1"></canvas>
                    </div><BR>
                </div>


            </div>
        </div>

    </div>
@endsection


@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        const pieData = {
            labels: ['STRATEGIS', 'TINGGI', 'RENDAH', 'BELUM DINILAI'],
            datasets: [{
                data: [{{ $strategis }}, {{ $tinggi }}, {{ $rendah }}, {{ $belum }}],
                backgroundColor: ['#dc3545', '#ffc107', '#28a745', '#6c757d'],
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
                        //return `${label}\n${value} (${percentage}%)`;
                        return `${percentage}%`;
                    }

                }
            }
        };

        new Chart(document.getElementById('pieChart1'), {
            type: 'pie',
            data: pieData,
            options: pieOptions,
            plugins: [ChartDataLabels]
        });
    </script>
@endpush
