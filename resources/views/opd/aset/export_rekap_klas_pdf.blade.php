<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Aset KAMI per Klas</title>
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
    </style>
</head>

<body>
    <div style="text-align:center; margin-bottom:20px;">
        <img src="{{ public_path('images/tlp/tlp_teaser_amber.png') }}" alt="TLP:AMBER+STRICT" width="150">
        {{-- <p style="font-weight:bold; color:#FFBF00; margin:0;">TLP:AMBER+STRICT</p> --}}
    </div>
    <h2>Rekap Aset KAMI per Klasifikasi - Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
    <h3>Pemilik Aset : {{ strtoupper($namaOpd) }}</h3>
    <BR>
    <h3 style="line-height:0;margin-bottom: 0">Klasifikasi Aset : {{ $klasifikasi->klasifikasiaset }}</h3>
    <div style="line-height:1.2; font-size: 0.9em">
        {{ $subs->pluck('subklasifikasiaset')->implode(', ') ?: '-' }}
    </div><BR>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">KODE ASET</th>
                <th style="width: 40%;">NAMA ASET</th>
                <th style="width: auto;">SUB KLASIFIKASI</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($asets as $aset)
                <tr>
                    <td>{{ $aset->kode_aset }}</td>
                    <td>{{ $aset->nama_aset }}</td>
                    <td>{{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table><BR><BR><BR>
    <h4>Catatan</h4>
    <ol>
        <li>Kode TLP (Traffic Light Protocol) dipakai untuk mengklasifikasikan sensitivitas informasi, supaya jelas
            sejauh mana informasi boleh dibagikan.
            <b>Kode TLP:AMBER berarti boleh dibagikan untuk komunitas tertentu sesuai kebutuhan.</b>
        </li>
        <li><b>PERISAI</b> adalah sistem elektronik untuk melakukan <span class="underline">PEngelolaan RISiko Aset
                Informasi,</span> dikelola oleh
            Bidang
            Persandian Dinas Kominfos Provinsi Bali</li>
        <li>Yang dimaksud dengan <b>Aset KAMI</b> dalam PERISAI adalah Aset Keamanan Informasi, yaitu <span
                class="underline">khusus aset yang terkait
                dengan pelindungan data dan keamanan informasi.</span>
        </li>
        <li><b>Pemutahiran data PERISAI wajib dilakukan minimal sekali setahun oleh Pemilik Aset.</b>
            Pemutahiran akan
            dilakukan serentak, menunggu informasi dari Dinas Kominfos Prov Bali. </li>
    </ol>
</body>

</html>
