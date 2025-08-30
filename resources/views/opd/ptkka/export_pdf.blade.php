<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Export PDF</title>
    <style>
        @page {
            margin: 170px 50px 70px 50px;
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .watermark {
            position: fixed;
            top: 40%;
            left: 5%;
            width: 100%;
            text-align: center;
            transform: rotate(-45deg);
            font-size: 200px;
            color: red;
            opacity: 0.07;
            z-index: -1000;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
        }

        .page-break {
            page-break-after: always;
        }

        .indikator {
            margin-bottom: 2px;
        }

        .tujuan {
            margin-top: 0;
            margin-bottom: 8px;
        }

        @page {
            margin: 170px 50px 70px 50px;
        }

        .header {
            position: fixed;
            top: -140px;
            left: 0px;
            right: 5px;
            text-align: left;
        }

        .header img.tlp {
            position: absolute;
            top: 0;
            right: -30;
            width: 150px;
        }

        .header .subs {
            margin-top: -5px;
            line-height: 1.2;
            font-size: 0.9em;
            margin-right: 0px;
            /* ruang kosong supaya teks turun & tidak timpa gambar */
        }

        .header h2,
        .header h3 {
            margin: 6px 0;
        }
    </style>
</head>

<body>

    {{-- Watermark --}}
    @if ($session->status != 4)
        <div class="watermark">DRAFT</div>
    @endif
    @php
        $kategoriLabel = [
            2 => 'WEB',
            3 => 'MOBILE',
        ];
    @endphp
    {{-- Konten PDF --}}

    @php
        $namaOpd = $session->aset->opd->namaopd ?? '-';
        $tanggalPengajuan = $session->created_at ? $session->created_at->format('d F Y') : '-';
    @endphp


    <div class="header">
        <table width="100%" style="border:none;">
            <tr>
                <!-- KIRI: judul + subs (tetap) -->
                <td style="vertical-align: top;border:none;">
                    <h2>Tingkat Kepatuhan Keamanan Aplikasi :: Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
                    <h3>Pemilik Risiko: {{ strtoupper($namaOpd) }}<br>
                        Nama Aset: {{ $session->aset->nama_aset }}</h3>
                    <div class="subs">
                        @if (isset($kategoriLabel[$session->standar_kategori_id]))
                            <span class="text-uppercase">Standar
                                {{ $kategoriLabel[$session->standar_kategori_id] }}
                            </span>
                        @endif / Tgl Mulai Pengisian : {{ $tanggalPengajuan }}</br>
                        {{-- Total Skor Kepatuhan: {{ $totalSkor }} = <strong>Kategori
                            {{ $kategoriKepatuhan }} ({{ $persentase }}%)</strong> --}}
                        </p>
                </td>

                <!-- KANAN: dua logo sejajar -->
                <td style="width: 100px; vertical-align: top; text-align: right; white-space: nowrap;border:none;">
                    <img src="{{ public_path('images/logobaliprovcsirt.png') }}" alt="Logo"
                        style="height:70px; vertical-align: top; margin-right:0px;">
                    <img src="{{ public_path('images/tlp/tlp_teaser_amber_strict.jpg') }}" alt="TLP:GREEN"
                        style="height:70px; vertical-align: top;">
                </td>
            </tr>
        </table>
        {{-- <p><strong>Subklasifikasi:</strong> {{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</p> --}}
        <hr>
    </div>


    {{-- ==================== RINGKASAN NILAI PER FUNGSI ==================== --}}
    <h3 style="margin: 0 0 6px 0;">Ringkasan Skor per Aspek</h3>

    <table width="100%" cellspacing="0" cellpadding="6" border="1"
        style="border-collapse: collapse; margin-bottom: 12px;">
        <thead>
            <tr>
                <th rowspan="2" class="text-center align-middle">ASPEK</th>
                <th colspan="2" class="text-center">JUMLAH REKOMENDASI</th>
                <th rowspan="2" class="text-center align-middle" style="width:15%;">SKOR PENERAPAN SEMENTARA</th>
            </tr>
            <tr>
                <th class="text-center" style="width:15%;">STANDAR</th>
                <th class="text-center" style="width:15%;">DIGUNAKAN</th>
            </tr>
        </thead>

        @php
            $skorPerFungsi = $skorPerFungsi ?? [];
            $totRekom = 0;
            $totDipakai = 0;
            $totSkor = 0;
            $semuaTerisi = true; // jadi false jika ada aspek dengan 'belum' > 0
        @endphp

        <tbody>
            @forelse ($skorPerFungsi as $row)
                @php
                    $total = (int) ($row['jumlah_rekomendasi_total'] ?? 0);
                    $dipakai = (int) ($row['jumlah_rekomendasi_dipakai'] ?? 0);
                    $skor = (int) ($row['skor_total'] ?? 0);
                    $belum = (int) ($row['jumlah_rekomendasi_belum'] ?? 0);
                    $hasBelum = $belum > 0;

                    $totRekom += $total;
                    $totDipakai += $dipakai;
                    $totSkor += $skor;

                    if ($hasBelum) {
                        $semuaTerisi = false;
                    }

                    // Dipakai: tampil "-" hanya bila semuanya belum diisi (dipakai==0 dan ada 'belum')
                    $dipakaiDisplay = $hasBelum && $dipakai === 0 ? 'BELUM DIISI' : $dipakai;

                    // Skor: jika masih ada 'belum' di aspek ini, tampil "-"
                    $skorDisplay = $hasBelum ? 'BELUM DIISI' : $skor;
                @endphp
                <tr>
                    <td>{{ chr(65 + $loop->index) }}.
                        {{ $row['fungsi_nama'] ?? 'Fungsi #' . ($row['fungsi_id'] ?? $loop->iteration) }}</td>
                    <td align="center">{{ $total }}</td>
                    <td align="center">{{ $dipakaiDisplay }}</td>
                    <td align="center">{{ $skorDisplay }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" align="center">Belum ada data.</td>
                </tr>
            @endforelse
        </tbody>

        @if (!empty($skorPerFungsi))
            <tfoot>
                <tr>
                    <th align="right">TOTAL</th>
                    <th align="center">{{ $totRekom }}</th>
                    <th align="center">{{ $semuaTerisi ? $totDipakai : '-' }}</th>
                    <th align="center">{{ $semuaTerisi ? $totSkor : '-' }}</th>
                </tr>
            </tfoot>
        @endif


    </table>

    <br>
    {{-- ================== /RINGKASAN NILAI PER FUNGSI ================== --}}

    {{--
    <h2 style="margin: 0;">{{ $session->aset->nama_aset ?? 'N/A' }} :: {{ $namaOpd }}</h2>
    <p style="margin: 0;">UID: {{ $session->uid }} @if (isset($kategoriLabel[$session->standar_kategori_id]))
            &nbsp;&middot;&nbsp;<span class="text-uppercase">[

                {{ $kategoriLabel[$session->standar_kategori_id] }} ]
            </span>
        @endif / Tgl Pengajuan : {{ $tanggalPengajuan }}
    </p>

    <p>Total Skor Kepatuhan: {{ $totalSkor }} = <strong>Kategori
            {{ $kategoriKepatuhan }} ({{ $persentase }}%)</strong>
    </p> --}}



    @foreach ($fungsiStandars as $fungsi)
        <h2>{{ chr(64 + $loop->iteration) }}. {{ $fungsi->nama }}</h2>


        @foreach ($fungsi->indikators as $i => $indikator)
            <p style="margin: 0;"><strong>{{ $i + 1 }}. {{ strtoupper($indikator->indikator) }}</strong></p>
            <p style="margin-top: 0; margin-bottom: 10px;font-style: italic">{{ $indikator->tujuan }}</p>

            @foreach ($indikator->rekomendasis as $rek)
                <div style="margin-left: 10px; margin-bottom: 10px;">
                    <p style="margin: 0;"><strong>REKOMENDASI</strong></p>
                    <p style="margin-top: 0; margin-bottom: 10px;">{{ $rek->rekomendasi }}</p>

                    <p style="margin: 0;"><strong>BUKTI DUKUNG YANG DISARANKAN</strong></p>
                    <p style="margin-top: 0; margin-bottom: 10px;">{{ $rek->buktidukung }}</p>

                    @php
                        $jawaban = $jawabans[$rek->id] ?? null;
                    @endphp

                    @php
                        $jawabanText = match ($jawaban->jawaban ?? null) {
                            3 => 'DITERAPKAN SELURUHNYA',
                            2 => 'DITERAPKAN SEBAGIAN',
                            1 => 'TIDAK DITERAPKAN',
                            0 => 'TIDAK RELEVAN (Tidak ada fitur yang membutuhkan Standar ini)',
                            default => '-',
                        };
                    @endphp

                    <p style="margin: 0;"><strong>Status Penerapan: </strong></p>
                    <p style="margin-top: 0; margin-bottom: 10px;">{{ $jawabanText }}</p>

                    <p style="margin: 0;"><strong>Penjelasan:</strong></p>
                    <p style="margin-top: 0; margin-bottom: 10px;">{{ $jawaban->penjelasanopd ?? '-' }}</p>

                    <p style="margin: 0;"><strong>Link Bukti Dukung:</strong></p>
                    @if (!empty($jawaban->linkbuktidukung))
                        <p style="margin-top: 0; margin-bottom: 10px;">
                            <a href="{{ $jawaban->linkbuktidukung }}" target="_self"
                                style="color: blue; text-decoration: underline;">
                                {{ $jawaban->linkbuktidukung }}
                            </a>
                        </p>
                    @else
                        <p style="margin-top: 0; margin-bottom: 10px;">-</p>
                    @endif
                    @if ($loop->last)
                        <hr style="border: none; border-top: 1px solid #000; margin: 10px 0;">
                    @else
                        <hr style="border: none; border-top: 1px dashed #000; margin: 10px 0;">
                    @endif


                </div>
            @endforeach
        @endforeach
    @endforeach



    {{-- Footer --}}
    {{-- <div class="footer">
        PERISAI :: Halaman
        <script type="text/php">
            if (isset($page)) {
                echo $page;
            }
        </script>
    </div> --}}





    <BR><BR>
    <h4>CATATAN</h4>
    <ol>
        <li>Kode TLP (Traffic Light Protocol) dipakai untuk mengklasifikasikan sensitifitas informasi, supaya jelas
            sejauh mana informasi boleh dibagikan.
            TLP:AMBER+STRICT berarti berisi informasi cukup sensitif. Hanya untuk internal organisasi penerima,
            tidak boleh
            keluar.
        </li>
        <li>PERISAI adalah sistem elektronik untuk melakukan <b>PE</b>ngelolaan <b>RIS</b>iko <b>A</b>set
            <b>I</b>nformasi di lingkup Pemerintah Provinsi Bali. PERISAI dikelola oleh
            Dinas Kominfos Provinsi Bali (Contact: Bidang Persandian)
        </li>
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan reviu dan pemutahiran data PERISAI yang
            dilakukan minimal sekali setahun oleh Pemilik Risiko. Pemutahiran akan dilakukan serempak, menunggu
            jadwal dari Diskominfos Prov Bali. </li>

    </ol>
</body>

</html>
