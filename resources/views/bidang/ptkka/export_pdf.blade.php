<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Export PDF</title>
    <style>
        h3 {
            margin: 0;
            padding: 0;
            line-height: 0;
        }

        h2 {
            margin-bottom: 5px;
        }

        @page {
            margin: 60px 30px;
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
            font-size: 150px;
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
            size: A4 portrait;
            margin: 60px 30px 70px 30px;
            /* top right bottom left */
        }
    </style>
</head>

<body>

    {{-- Watermark --}}
    @if ($session->status != 4)
        <div class="watermark">VERIFIKASI</div>
    @endif
    @php
        $kategoriLabel = [
            2 => 'WEB',
            3 => 'MOBILE',
        ];
    @endphp
    {{-- Konten PDF --}}

    @php
        use Carbon\Carbon;
        $namaOpd = $session->aset->opd->namaopd ?? '-';
        $tanggalPengajuan = $session->created_at ? Carbon::parse($session->created_at)->translatedFormat('d F Y') : '-';
        $tanggalVerif = $session->updated_at ? Carbon::parse($session->updated_at)->translatedFormat('d F Y') : '-';
    @endphp
    <h1>
        <center>**DOKUMEN RAHASIA**</center>
    </h1><BR>
    <h2 style="margin: 0;">{{ $session->aset->nama_aset ?? 'N/A' }} :: {{ $namaOpd }}</h2>
    <p style="margin: 0;">UID: {{ $session->uid }} @if (isset($kategoriLabel[$session->standar_kategori_id]))
            &nbsp;&middot;&nbsp;<span class="text-uppercase">[ Standar:

                {{ $kategoriLabel[$session->standar_kategori_id] }} ]
            </span>
        @endif
    </p>
    <p style="margin: 0;">
        Tgl Pengajuan : {{ $tanggalPengajuan }} /
        @if ($session->status == 4)
            Tgl Rampung PTKKA : {{ $tanggalVerif }}
        @else
            Tgl Mulai Verifikasi : {{ $tanggalVerif }}
        @endif
    </p>


    <p style="margin: 0;">Total Skor Kepatuhan
        @if ($session->status == 4)
            : {{ $totalSkor }} = <strong>Kategori
                {{ $kategoriKepatuhan }} ({{ $persentase }}%)</strong>
        @else
            (sementara)
            : {{ $totalSkor }} = <strong>Kategori
                {{ $kategoriKepatuhan }} ({{ $persentase }}%)</strong>
        @endif

    </p>

    <hr>
    <BR>
    {{-- ==================== RINGKASAN NILAI PER FUNGSI ==================== --}}
    <h3 style="margin: 0 0 6px 0;">Ringkasan Skor per Aspek</h3>
    <table width="100%" cellspacing="0" cellpadding="6" border="1"
        style="border-collapse: collapse; margin-bottom: 12px;">
        <thead>
            <tr>
                <th align="left">ASPEK</th>
                <th align="center" style="width:15%;">JML REKOM</th>
                <th align="right" style="width:20%;">TINGKAT KEPATUHAN</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Antisipasi jika variabel belum ada
                $skorPerFungsi = $skorPerFungsi ?? [];
            @endphp

            @forelse ($skorPerFungsi as $row)
                <tr>
                    <td>{{ chr(65 + $loop->index) }}. {{ $row['fungsi_nama'] }}</td>
                    <td align="center">{{ $row['jumlah_rekomendasi'] }}</td>
                    <td align="right">{{ $row['skor_total'] }} ({{ number_format($row['persentase'], 2) }}%)
                        : {{ $row['kategori'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" align="center">Belum ada data fungsi/indikator pada kategori ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{-- ================== /RINGKASAN NILAI PER FUNGSI ================== --}}
    <hr></br></br>
    <h3 style="margin: 0 0 6px 0;">Detil Skor per Aspek</h3>

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
                            2 => 'DITERAPKAN SELURUHNYA',
                            1 => 'DITERAPKAN SEBAGIAN',
                            0 => 'TIDAK DITERAPKAN',
                            default => '-',
                        };
                    @endphp

                    <p style="margin: 0;"><strong>Jawaban Pemilik Aset</strong></p>
                    <p style="margin-top: 0; margin-bottom: 10px;">{{ $jawabanText }}</p>

                    <p style="margin: 0;"><strong>Penjelasan Pemilik Aset</strong></p>
                    <p style="margin-top: 0; margin-bottom: 10px;">{{ $jawaban->penjelasanopd ?? '-' }}</p>

                    <p style="margin: 0;"><strong>Link Bukti</strong></p>
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

                    <p style="margin: 0;"><strong>CATATAN DISKOMINFOS PROV BALI</strong></p>
                    <p style="margin-top: 0; margin-bottom: 10px;">{{ $jawaban->catatanadmin ?? '-' }}</p>

                    @if ($loop->last)
                        <hr style="border: none; border-top: 1px solid #000; margin: 10px 0;">
                    @else
                        <hr style="border: none; border-top: 1px dashed #000; margin: 10px 0;">
                    @endif


                </div>
            @endforeach
        @endforeach
    @endforeach

    <BR><BR><BR>
    <h4>Catatan</h4>
    <ol>
        <li>Dokumen Hasil Penilaian Tingkat Kepatuhan Keamanan Aplikasi (PTKKA) ini adalah bersifat <b>RAHASIA</b>.</li>
        <li>Simpan dokumen ini dengan baik sebagai bukti pelaksaan PTKKA.</li>
        <li>Segera musnahkan jika dokumen ini sudah selesai digunakan.</li>
        <li>PTKKA dilakukan minimal 1 kali dan setahun </li>
    </ol>








</body>

</html>
