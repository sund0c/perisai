<!DOCTYPE html>
<html>

<head>
    <title>Hasil Penilaian Kategori SE</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table th,
        table td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #eeeeee;
        }

        .font-dejavu {
            font-family: 'DejaVu Sans', sans-serif;
        }

        .f12 {
            font-size: 14px;
        }

        h2,
        h3,
        h4,
        p {
            margin: 0;
            padding: 0;
        }

        h3 {
            margin-bottom: 10px;
        }

        h4 {
            margin-bottom: 2px;
            padding-bottom: 0;
        }

        ol {
            margin-top: 0;
            padding-top: 0;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .underline {
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        td {
            border: 1px solid #999;
            padding: 6px;
            vertical-align: top;
        }

        .label {
            width: 20%;
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .value {
            width: 60%;
        }
    </style>
</head>

<body>
    <h1>
        <center>**DOKUMEN RAHASIA**</center>
    </h1><BR>
    <h2>Hasil Penilaian Kategori SE - Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
    <h3>Pemilik Aset : {{ strtoupper($aset->opd->namaopd) }}</h3>

    <p><strong>Nama Aset: {{ $aset->nama_aset }}</strong></p>
    <p><strong>Subklasifikasi:</strong> {{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</p>

    <table>
        <thead>
            <tr>
                <th width="50%">INDIKATOR</th>
                <th width="50%">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($indikators as $indikator)
                @php
                    $kodeJawaban = strtoupper($kategoriSe->jawaban[$indikator->kode]['jawaban'] ?? '');
                    $keterangan = $kategoriSe->jawaban[$indikator->kode]['keterangan'] ?? '';

                    // Ambil teks opsi sesuai pilihan
                    $jawaban = match ($kodeJawaban) {
                        'A' => $indikator->opsi_a,
                        'B' => $indikator->opsi_b,
                        'C' => $indikator->opsi_c,
                        default => '-',
                    };
                @endphp

                <tr>
                    <td>{{ $indikator->kode }}. {{ $indikator->pertanyaan }}</td>
                    <td>
                        {{ $jawaban }}<br>
                        @if ($keterangan)
                            <em>{{ $keterangan }}</em>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table><BR>

    <p>
        <strong>Kategori SE:
            {{ strtoupper($kategoriLabel) }} (Skor:{{ $skor }})</strong>

    </p>
    <BR><BR><BR>
    <h4>Catatan</h4>
    <ol>
        <li>Dokumen Hasil Penilaian Kategori SE ini adalah bersifat <b>RAHASIA</b>.</li>
        <li>Musnahkan jika dokumen ini sudah selesai digunakan.</li>
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan review dan pemutahiran data PERISAI <b>yang
                dilakukan sekali setahun oleh Pemilik Aset.</b> </li>
    </ol>
</body>

</html>
