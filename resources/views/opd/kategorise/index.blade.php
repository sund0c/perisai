@extends('adminlte::page')

@section('title', 'Rekap Kategori SE')


@section('content_header')
    <h1>Kategori Sistem Elektronik</h1>
    <div style="line-height:1.2; font-size: 0.9em">
        Sistem elektronik (SE) dalam PERISAI adalah <strong>aset dengan klasifikasi [PL] Perangkat
            Lunak.</strong> Contoh SE adalah
        website, aplikasi
        berbasis web, mobile, sistem operasi dan utility.
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
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-3" style="gap: 10px;">
                        <a href="{{ route('opd.kategorise.export_rekap') }}" class="btn btn-danger btn-sm">

                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                    <table class="table table-bordered text-center" style="width:100%">
                        <thead class="font-weight-bold">
                            <tr>
                                <th style="width:80%">KATEGORI SE</th>
                                <th>JUMLAH ASET</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-danger text-white" style="text-align: left">
                                <td style="text-align: left"><b>TINGGI</b>
                                    <div class="mt-1" style="font-size:.9rem;line-height:1.2">
                                        {{ data_get($kategoriMeta, 'TINGGI.deskripsi', '-') }}
                                    </div>
                                </td>
                                <td style="vertical-align: middle; text-align: center;">
                                    <a class="btn btn-light btn-sm px-4"
                                        href="{{ route('opd.kategorise.show', ['kategori' => 'tinggi']) }}">
                                        {{ $tinggi }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="bg-warning text-white" style="text-align: left">
                                <td style="text-align: left"><b>SEDANG</b>
                                    <div class="mt-1" style="font-size:.9rem;line-height:1.2">
                                        {{ $kategoriMeta['SEDANG']['deskripsi'] ?? '-' }}
                                    </div>
                                </td>
                                <td style="vertical-align: middle; text-align: center;">
                                    <a class="btn btn-light btn-sm px-4"
                                        href="{{ route('opd.kategorise.show', ['kategori' => 'sedang']) }}">
                                        {{ $sedang }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="bg-success text-white" style="text-align: left">
                                <td style="text-align: left"><b>RENDAH</b>
                                    <div class="mt-1" style="font-size:.9rem;line-height:1.2">
                                        {{ $kategoriMeta['RENDAH']['deskripsi'] ?? '-' }}
                                    </div>
                                </td>
                                <td style="vertical-align: middle; text-align: center;">
                                    <a class="btn btn-light btn-sm px-4"
                                        href="{{ route('opd.kategorise.show', ['kategori' => 'rendah']) }}">
                                        {{ $rendah }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="bg-secondary text-white" style="text-align: left">
                                <td style="text-align: left"><b>Belum Dinilai</b>
                                    <div class="mt-1" style="font-size:.9rem;line-height:1.2">
                                        {{ $kategoriMeta['BELUM']['deskripsi'] ?? '-' }}
                                    </div>
                                </td>
                                <td style="vertical-align: middle; text-align: center;">
                                    <a class="btn btn-light btn-sm px-4"
                                        href="{{ route('opd.kategorise.show', ['kategori' => 'belum']) }}">
                                        {{ $belum }}
                                    </a>
                                </td>
                            </tr>
                            <tr class="font-weight-bold">
                                <td>Total</td>
                                <td>{{ $total }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-3">
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
            labels: ['TINGGI', 'SEDANG', 'RENDAH', 'BELUM DINILAI'],
            datasets: [{
                data: [{{ $tinggi }}, {{ $sedang }}, {{ $rendah }}, {{ $belum }}],
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
