@extends('adminlte::page')

@section('title', 'Rekap Aset per Klasifikasi')

@section('content_header')
    <h1>Aset {{ strtoupper($namaOpd) }}</h1>
    <p class="text-muted small mb-0">
        Aset yang dimaksud dalam PERISAI adalah <strong>aset TIK yang berhubungan dengan pelindungan data dan keamanan
            informasi.</strong>
        Contoh asetnya
        adalah komputer karena menyimpan data dan berpotensi data di dalamnya bocor. Mesin Ketik elektronik (bukan
        komputer), Air Conditioner
        (AC), mesing penghancur kertas, dan sejenisnya, tidak perlu dimasukkan sebagai aset dalam MANA-KAMI karena tidak
        terhubung
        langsung dengan pelindungan data dan keamanan informasi.

    </p>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
            @if ($kunci === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @endif
        </span>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-3" style="gap: 10px;">
                        <a href="{{ route('opd.aset.export_rekap') }}" class="btn btn-danger btn-sm">
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
                                        <a href="{{ route('opd.aset.show_by_klasifikasi', $klasifikasi->id) }}">
                                            [{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }}
                                        </a>
                                    </td>
                                    <td>{{ $klasifikasi->jumlah_aset ?? 0 }}</td>
                                    <td style="background-color: #FF0000; color: white;">
                                        {{ $klasifikasi->jumlah_tinggi ?? 0 }}
                                    </td>
                                    <td style="background-color: #FFD700; color: black;">
                                        {{ $klasifikasi->jumlah_sedang ?? 0 }}</td>
                                    <td style="background-color: #00B050; color: white;">
                                        {{ $klasifikasi->jumlah_rendah ?? 0 }}</td>
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

                </div>
            </div>
        </div>
        <div class="col-md-6">

            <div class="card">
                <div class="card-body text-center">
                    <div style="width: 100%;margin: auto;">
                        <canvas id="pieChart"></canvas>
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
