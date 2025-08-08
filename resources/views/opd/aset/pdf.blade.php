<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Aset KAMI</title>
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
        <center>RAHASIA</center>
    </h1><BR>
    <h2>Informasi Aset - Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
    <h3>Pemilik Aset : {{ strtoupper($namaOpd) }}</h3>

    {{--
    @foreach ($fieldList as $field)
        @includeIf('opd.aset.fields.' . $field, ['aset' => $aset])
    @endforeach --}}


    <table>
        @foreach ($fieldList as $field)
            @php
                $label = ucwords(str_replace('_', ' ', $field));
                $value =
                    $field === 'subklasifikasiaset_id'
                        ? $aset->subklasifikasiaset->subklasifikasiaset ?? '-'
                        : $aset->$field ?? '-';
            @endphp
            <tr>
                <td class="label">{{ $label }}</td>
                <td class="value">{{ $value }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="label">NILAI ASET</td>
            <td class="value"><strong>{{ $aset->nilai_akhir_aset }}</strong></td>
        </tr>
    </table><BR><BR><BR>
    <h4>Catatan</h4>
    <ol>
        <li>Dokumen informasi Aset ini adalah bersifat <b>RAHASIA</b>.</li>
        <li>Musnahkan jika dokumen ini sudah selesai digunakan.</li>
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan review dan pemutahiran data MANA-KAMI <b>yang
                dilakukan sekali setahun oleh Pemilik Aset.</b> </li>
    </ol>

</body>

</html>
