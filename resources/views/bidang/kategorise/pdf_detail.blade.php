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
        {
        margin: 0;
        padding: 0;
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

        @page {
            margin: 190px 50px 70px 50px;
        }

        .header {
            position: fixed;
            top: -160px;
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
</head>

<body>
    <div class="header">
        <table width="100%" style="border:none;">
            <tr>
                <!-- KIRI: judul + subs (tetap) -->
                <td style="vertical-align: top;border:none;">
                    <h2>Rekap Kategori SE :: Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
                    <h3>Pemilik Risiko: {{ $aset->opd->namaopd }}</h3>
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
        <p><strong>Kode Aset: {{ $aset->kode_aset }}</strong></p>
        <p><strong>Nama Aset: {{ $aset->nama_aset }}</strong></p>
        <p><strong>Subklasifikasi:</strong> {{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</p>
    </div>

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
    <BR>
    <h4>A. KETERANGAN KATEGORI SE</h4>
    <ol>
        <li><b>STRATEGIS:</b> Berdampak serus terhadap kepentingan umum, pelayanan publik, kelancaran penyelenggaraan
            negara atau pertahanan dan keamanan negara. Wajib menerapkan TIGA STANDAR KEAMANAN yaitu SNI ISO/IEC 27001,
            Standar keamanan siber dari BSSN dan Standar keamanan siber lainnya dari Kementrian/Lembaga.</li>
        <li><b>TINGGI:</b> Berdampak terbatas pada kepentingan sektor dan/atau daerah tertentu. Wajib menerapkan DUA
            STANDAR KEAMANAN yaitu SNI ISO/IEC 27001 atau standar keamanan siber dari BSSN, dan Standar keamanan siber
            lainnya dari Kementrian/Lembaga.</li>
        <li><b>RENDAH:</b> Wajib menerapkan SATU STANDAR KEAMANAN yaitu SNI ISO/IEC 27001 atau standar keamanan dari
            BSSN.</li>

    </ol><BR>
    <h4>B CATATAN</h4>
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
        <li>SE adalah sistem elektronik yaitu dalam PERISAI adalah Aset Informasi dengan klasifikasi [PL] Perangkat
            Lunak.</li>
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan reviu dan pemutahiran data PERISAI yang
            dilakukan minimal sekali setahun oleh Pemilik Risiko. Pemutahiran akan dilakukan serempak, menunggu
            jadwal dari Diskominfos Prov Bali. </li>
</body>

</html>
