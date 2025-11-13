@extends('adminlte::page')

@section('title', 'Rekap Kategori SE')

@section('content_header')
    <h1>Kategorisasi Sistem Elektronik (SE) di Pemprov Bali</h1>
    <p class="text-muted small mb-0">
        SE adalah sistem elektronik yaitu dalam PERISAI adalah <strong>aset dengan klasifikasi [PL] Perangkat
            Lunak.</strong> Contoh SE adalah
        website, aplikasi
        berbasis web, mobile, sistem operasi dan utility.
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
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-3" style="gap: 10px;">
                        <a href="{{ route('bidang.kategorise.export_rekap') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                    <table class="table table-bordered text-center" style="width:100%">
                        <thead class="font-weight-bold">
                            <tr>
                                <th style="width:200px">KATEGORI SE</th>
                                <th>JUMLAH ASET</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="bg-danger text-white" style="text-align: left">TINGGI</td>
                                <td>
                                    <a href="{{ route('bidang.kategorise.show', ['kategori' => 'tinggi']) }}">
                                        {{ $tinggi }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-warning" style="text-align: left">SEDANG</td>
                                <td>
                                    <a href="{{ route('bidang.kategorise.show', ['kategori' => 'sedang']) }}">
                                        {{ $sedang }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-success text-white" style="text-align: left">RENDAH</td>
                                <td>
                                    <a href="{{ route('bidang.kategorise.show', ['kategori' => 'rendah']) }}">
                                        {{ $rendah }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-secondary text-white" style="text-align: left">Belum Dinilai</td>
                                <td>
                                    <a href="{{ route('bidang.kategorise.show', ['kategori' => 'belum']) }}">
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <div style="width: 50%;margin: auto;">
                        <canvas id="pieChart1"></canvas>
                    </div>
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
                        return `${label}\n${value} (${percentage}%)`;
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
