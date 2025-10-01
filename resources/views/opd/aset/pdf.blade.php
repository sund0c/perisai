<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Aset Informasi</title>
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

        @page {
            margin: 160px 50px 70px 50px;
        }


        .header {
            position: fixed;
            top: -110px;
            left: 0px;
            right: 0px;
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
                    <h2 style="margin:0;">Informasi dan Nilai Aset Informasi :: Tahun {{ $tahunAktifGlobal ?? '-' }}
                    </h2>
                    <h3 style="margin:0;">Pemilik Risiko: {{ strtoupper($namaOpd) }}</h3>

                    <div class="subs" style="margin-top:2px;">
                        Pemilik Risiko bertanggungjawab terhadap proses bisnis/layanannya dengan cara pengelolaan aset
                        yaitu dari
                        mulai melakukan Pengukuran Nilai Aset, Kategorisasi SE termasuk pemenuhan standar, Pemetaan
                        Risiko, Analisa
                        Risiko, pembuatan Rencana Tindak Lanjut dan implementasi mitigasi risiko, sampai menjadi
                        <i>Lead Auditee</i> dalam Audit Keamanan
                    </div>
                </td>

                <!-- KANAN: dua logo sejajar -->
                <td style="width: 100px; vertical-align: top; text-align: right; white-space: nowrap;border:none;">
                    <img src="{{ public_path('images/logobaliprovcsirt.png') }}" alt="Logo"
                        style="height:70px; vertical-align: top; margin-right:0px;">
                    <img src="{{ public_path('images/tlp/tlp_teaser_amber_strict.jpg') }}" alt="TLP:AMBER+STRICT"
                        style="height:70px; vertical-align: top;">
                </td>
            </tr>
        </table>
    </div>


    {{-- <div class="header">
        <img src="{{ public_path('images/tlp/tlp_teaser_amber_strict.png') }}" alt="TLP:AMBER+STRICT" class="tlp">
        <h2>Informasi dan Nilai Aset Informasi - Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
        <h3>
            Pemilik Risiko: {{ strtoupper($namaOpd) }}</h3>
        <div class="subs">
            Pemilik Risiko bertanggungjawab terhadap proses bisnis/layanannya dengan cara pengelolaan aset yaitu dari
            mulai
            melakukan Pengukuran Nilai Aset, Kategorisasi SE termasuk pemenuhan standar, Pemetaan Risiko, Analisa
            Risiko,
            pembuatan Rencana Tindak Lanjut dan implementasi mitigasi risiko, sampai menjadi <i>Lead Auditee</i> dalam
            Audit Keamanan

        </div>

    </div> --}}
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

                // Daftar field CIAAA + kode singkat
                $ciaaaMap = [
                    'kerahasiaan' => 'C',
                    'integritas' => 'I',
                    'ketersediaan' => 'A',
                    'keaslian' => 'A',
                    'kenirsangkalan' => 'N',
                ];
            @endphp
            <tr>
                <td class="label">
                    @if (array_key_exists($field, $ciaaaMap))
                        Tingkat {{ $label }} ({{ $ciaaaMap[$field] }})
                    @else
                        {{ $label }}
                    @endif
                </td>
                <td class="value">
                    {{ $value }}
                    @if (array_key_exists($field, $ciaaaMap))
                        <span class="text-muted"> dari 3</span>
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td class="label">NILAI ASET (CIAAN)</td>
            <td class="value"><strong>{{ $aset->nilai_akhir_aset }}</strong></td>
        </tr>
    </table><BR><BR><BR>
    <h4>Catatan</h4>
    <ol>
        <li>Dokumen informasi Aset ini adalah bersifat <b>RAHASIA</b>.</li>
        <li>Musnahkan jika dokumen ini sudah selesai digunakan.</li>
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan review dan pemutahiran data PERISAI <b>yang
                dilakukan sekali setahun oleh Pemilik Aset.</b> </li>
    </ol>

</body>

</html>
