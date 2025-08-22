@extends('adminlte::page')

@section('title', 'Rekap Aset per Klasifikasi')

@section('content_header')
    <h1>Aset</h1>
    <div style="line-height:1.2; font-size: 0.9em">
        Aset yang dimaksud dalam PERISAI adalah <strong>aset TIK yang berhubungan dengan pelindungan data dan keamanan
            informasi.</strong>
        Contoh asetnya
        adalah komputer karena menyimpan data dan berpotensi data di dalamnya bocor. Mesin Ketik elektronik (bukan
        komputer), Air Conditioner
        (AC), mesing penghancur kertas, dan sejenisnya, tidak perlu dimasukkan sebagai aset dalam PERISAI karena tidak
        terhubung
        langsung dengan pelindungan data dan keamanan informasi.

    </div>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif {{ $tahunAktifGlobal ?? '-' }}
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
                        <a href="{{ route('opd.aset.export_rekap') }}" class="btn btn-danger btn-sm mb-3 me-2">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        @if ($kunci != 'locked')
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

                    {{-- <table id="asetklasTable" class="table table-bordered table-hover text-center" --}}
                    <table class="table table-bordered text-center" style="width:100%;">
                        <thead>
                            <tr>
                                <th rowspan="2" style="vertical-align: middle; text-align: center;">Klasifikasi Aset</th>
                                <th style="width:10%;vertical-align: middle; text-align: center;" rowspan="2">Jumlah Aset
                                </th>
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
                                    <td style="text-align: left;height: 70px;">
                                        <a href="{{ route('opd.aset.show_by_klasifikasi', $klasifikasi->id) }}">
                                            [{{ $klasifikasi->kodeklas }}] {{ $klasifikasi->klasifikasiaset }}
                                            <div style="font-size:.9em;color:#666;line-height:1.2">
                                                {{ $klasifikasi->subklasifikasi->pluck('subklasifikasiaset')->implode(', ') ?: '-' }}
                                            </div>


                                        </a>
                                    </td>
                                    <td style="vertical-align: middle; text-align: center;">
                                        {{ $klasifikasi->jumlah_aset ?? 0 }}</td>
                                    <td
                                        style="background-color: #FF0000; color: white;vertical-align: middle; text-align: center;">
                                        {{ $klasifikasi->jumlah_tinggi ?? 0 }}
                                    </td>
                                    <td
                                        style="background-color: #FFD700; color: black;vertical-align: middle; text-align: center;">
                                        {{ $klasifikasi->jumlah_sedang ?? 0 }}</td>
                                    <td
                                        style="background-color: #00B050; color: white;vertical-align: middle; text-align: center;">
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
                                <td style="height:70px;vertical-align: middle; text-align: center;">Total</td>
                                <td style="height:70px;vertical-align: middle; text-align: center;">
                                    {{ $klasifikasis->sum('jumlah_aset') }}</td>
                                <td
                                    style="background-color: #FF0000; color: white;height:70px;vertical-align: middle; text-align: center;">
                                    {{ $klasifikasis->sum('jumlah_tinggi') }}</td>
                                <td
                                    style="background-color: #FFD700; color: black;height:70px;vertical-align: middle; text-align: center;">
                                    {{ $klasifikasis->sum('jumlah_sedang') }}</td>
                                <td
                                    style="background-color: #00B050; color: white;height:70px;vertical-align: middle; text-align: center;">
                                    {{ $klasifikasis->sum('jumlah_rendah') }}</td>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
        <div class="col-md-3">

            <div class="card">
                <div class="card-body">
                    <div style="width: 100%;margin: auto;">
                        <canvas id="pieChart"></canvas>
                    </div><BR>

                    <div style="font-size: 0.8em">
                        <b>KETERANGAN NILAI ASET</b>
                        <ol style="padding-left: 20px; margin-left: 0;">
                            @foreach ($ranges as $range)
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
                        //return `${label}\n${value} (${percentage}%)`;
                        return `${percentage}%`;
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
