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
            margin: 200px 50px 70px 50px;
        }


        .header {
            position: fixed;
            top: -150px;
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
            margin-right: 100px;
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
        <img src="{{ public_path('images/tlp/tlp_teaser_amber_strict.png') }}" alt="TLP:AMBER+STRICT" class="tlp">
        {{-- <p style="font-weight:bold; color:#FFBF00; margin:0;">TLP:AMBER+STRICT</p> --}}
        <h2>Rekap Kategori SE ({{ strtoupper($kategoriLabel) }}) :: Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
        <h3>Pemilik Risiko: {{ strtoupper($aset->opd->namaopd) }}</h3>

        <div class="subs">
            Pemilik Risiko bertanggungjawab terhadap proses bisnis/layanannya dengan cara pengelolaan aset yaitu dari
            mulai
            melakukan Pengukuran Nilai Aset, Kategorisasi SE termasuk pemenuhan standar, Pemetaan Risiko, Analisa
            Risiko,
            pembuatan Rencana Tindak Lanjut dan implementasi mitigasi risiko, sampai menjadi <i>Lead Auditee</i> dalam
            Audit Keamanan
            {{-- {{ $subs->pluck('subklasifikasiaset')->implode(', ') ?: '-' }} --}}
        </div>
        <h3></h3>
        <p><strong>Nama Aset: {{ $aset->nama_aset }}</strong></p>
        <p><strong>Subklasifikasi:</strong> {{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</p>
    </div>



    <table>
        <thead>
            <tr>
                <th style="text-align: center;width:7%">NO</th>
                <th width="50%">INDIKATOR</th>
                <th>KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @php $no=1;@endphp
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
                    <td style="text-align: center">{{ $no++ }}</td>
                    <td>{{ $indikator->pertanyaan }}</td>
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
        <li>Kode TLP (Traffic Light Protocol) dipakai untuk mengklasifikasikan sensitifitas informasi, supaya jelas
            sejauh mana informasi boleh dibagikan.
            <b>Kode TLP:AMBER+STRICT berarti berisi informasi cukup sensitif. Hanya untuk internal organisasi penerima,
                tidak boleh
                keluar.</b>
        </li>
        <li><b>PERISAI</b> adalah sistem elektronik untuk melakukan <b>PE</b>ngelolaan <b>RIS</b>iko <b>A</b>set
            <b>I</b>nformasi di lingkup Pemerintah Provinsi Bali. PERISAI dikelola oleh
            Dinas Kominfos Provinsi Bali (Contact: Bidang Persandian)
        </li>
        <li>Aset dalam PERISAI adalah <strong>ASET INFORMASI yang mendukung kinerja organisasi dalam menjalakan proses
                bisnis/layanannya.</strong>
        </li>
        <li>SE adalah sistem elektronik yaitu dalam PERISAI adalah <strong>aset dengan klasifikasi [PL] Perangkat
                Lunak.</strong> Contoh SE adalah
            website, aplikasi
            berbasis web, mobile, sistem operasi dan utility.</li>
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan review dan pemutahiran data PERISAI <b>yang
                dilakukan minimal sekali setahun oleh Pemilik Risiko. Pemutahiran akan dilakukan serempak, menunggu
                informasi dari Diskominfos Prov Bali.</b> </li>
    </ol>
</body>

</html>
