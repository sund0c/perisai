<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Export PDF</title>
    <style>
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


    <p>Total Skor Kepatuhan
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

    {{-- Footer --}}
    {{-- <div class="footer">
        PERISAI :: Halaman
        <script type="text/php">
            if (isset($page)) {
                echo $page;
            }
        </script>
    </div> --}}






</body>

</html>
