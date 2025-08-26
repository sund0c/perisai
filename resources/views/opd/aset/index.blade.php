@extends('adminlte::page')

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
    <h1>Aset</h1>
    <div style="line-height:1.2; font-size: 0.9em">
        Aset dalam PERISAI adalah <strong>ASET INFORMASI yang mendukung kinerja organisasi dalam menjalakan proses
            bisnis/layanannya.</strong>
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
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div style="gap: 10px;">
                        <a href="{{ route('opd.aset.export_rekap') }}" class="btn btn-danger btn-sm mb-3 me-2">
                            <i class="fas fa-file-pdf"></i> Export PDF</a>

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
                                        {{-- Kirim param dengan key "klasifikasiaset" (bisa objek model atau id) --}}
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

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        (function() {
            const el = document.getElementById('pieChart');
            if (!el) return;

            // Ambil angka dari data-* agar aman dari null/undefined
            const tinggi = parseInt(el.dataset.tinggi || '0', 10);
            const sedang = parseInt(el.dataset.sedang || '0', 10);
            const rendah = parseInt(el.dataset.rendah || '0', 10);

            const pieData = {
                labels: ['TINGGI', 'SEDANG', 'RENDAH'],
                datasets: [{
                    data: [tinggi, sedang, rendah],
                    backgroundColor: ['#dc3545', '#ffc107', '#28a745'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            };

            const pieOptions = {
                responsive: true,
                maintainAspectRatio: true,
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
                            const percentage = total ? Math.round((value / total) * 100) : 0;
                            return `${percentage}%`;
                        }
                    }
                }
            };

            new Chart(el, {
                type: 'pie',
                data: pieData,
                options: pieOptions,
                plugins: [ChartDataLabels]
            });
        })();
    </script>
@endpush
