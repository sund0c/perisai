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
    <div style="text-align:center; margin-bottom:20px;">
        <img src="{{ public_path('images/tlp/tlp_teaser_amber_strict.png') }}" alt="TLP:AMBER+STRICT" width="150">
        {{-- <p style="font-weight:bold; color:#FFBF00; margin:0;">TLP:AMBER+STRICT</p> --}}
    </div>
    <BR>
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
                // Daftar field CIAAA
                $ciaaaFields = ['kerahasiaan', 'integritas', 'ketersediaan', 'keaslian', 'kenirsangkalan'];
            @endphp
            <tr>
                <td class="label">
                    {{ in_array($field, $ciaaaFields) ? 'Tingkat ' . $label : $label }}
                </td>
                <td class="value">
                    {{ $value }}
                    @if (in_array($field, $ciaaaFields))
                        <span class="text-muted"> dari 3</span>
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td class="label">NILAI ASET</td>
            <td class="value"><strong>{{ $aset->nilai_akhir_aset }}</strong></td>
        </tr>
    </table><BR>
    <h4>KETERANGAN NILAI ASET</h4>
    <ol>
        @foreach ($ranges as $range)
            <li><b>{{ $range->nilai_akhir_aset }} :</b> {{ $range->deskripsi }}</li>
        @endforeach
    </ol><BR>
    <h4>Catatan</h4>
    <ol>
        <li>Kode TLP (Traffic Light Protocol) dipakai untuk mengklasifikasikan sensitivitas informasi, supaya jelas
            sejauh mana informasi boleh dibagikan.
            <b>Kode TLP:AMBER+STRICT berarti berisi informasi cukup sensitif. Hanya untuk internal organisasi penerima,
                tidak boleh
                keluar.</b>
        </li>
        <li><b>PERISAI</b> adalah sistem elektronik untuk melakukan <span class="underline">PEngelolaan RISiko Aset
                Informasi,</span> dikelola oleh
            Bidang
            Persandian Dinas Kominfos Provinsi Bali</li>
        <li>Yang dimaksud dengan <b>Aset KAMI</b> dalam PERISAI adalah Aset Keamanan Informasi, yaitu <span
                class="underline">khusus aset yang terkait
                dengan pelindungan data dan keamanan informasi.</span>
        </li>
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan review dan pemutahiran data PERISAI <b>yang
                dilakukan minimal sekali setahun oleh Pemilik Aset. Pemutahiran akan dilakukan serempak, menunggu
                informasi dari Diskominfos Prov Bali.</b> </li>
    </ol>

</body>

</html>
