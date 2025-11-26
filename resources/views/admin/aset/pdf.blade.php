<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Detil Aset TIK</title>
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


    <table>
        @foreach ($fieldList as $field)
            @php
                $label =
                    $field === 'nip_personil'
                        ? 'Nama Personil'
                        : ($field === 'link_pse'
                            ? 'Link PSE'
                            : ($field === 'subklasifikasiaset_id'
                                ? 'Sub Klasifikasi Aset'
                                : ($field === 'keterangan'
                                    ? 'Keterangan / Fungsi'
                                    : ($field === 'fungsi_personil'
                                        ? 'Kabid/Kabag'
                                        : ($field === 'unit_personil'
                                            ? 'Seksi/Tim'
                                            : ucwords(str_replace('_', ' ', $field)))))));
                $value =
                    $field === 'subklasifikasiaset_id'
                        ? $aset->subklasifikasiaset->subklasifikasiaset ?? '-'
                        : $aset->$field ?? '-';

                if (in_array($field, ['link_url', 'link_pse']) && $value === '-') {
                    $value = '';
                }
            @endphp
            <tr>
                <td class="label">{{ $label }}</td>
                @php
                // $labels = [
                //    1 => 'Tidak Signifikan',
                //    2 => 'Penting',
                //    3 => 'Sangat Penting',
                // ];
    $labels = [
        1 => 'Rendah',
        2 => 'Sedang',
        3 => 'Tinggi'
    ];
    $isLinkField = in_array($field, ['link_url', 'link_pse']) && filter_var($value, FILTER_VALIDATE_URL);
    $displayUrl = $value;
    if ($isLinkField) {
        $displayUrl = preg_replace('#^https?://#i', '', $value);
    }
@endphp

<td class="value">
    @if ($isLinkField)
        <a href="{{ $value }}" target="_blank" rel="noopener"
            style="text-decoration:none;color:#000;">{{ $displayUrl }}</a>
    @else
        {{ $labels[$value] ?? ($value === '' ? '-' : $value) }}
    @endif
</td>

            </tr>
        @endforeach
        <tr>
            <td class="label">NILAI ASET</td>
            <td class="value"><strong>{{ $aset->nilai_akhir_aset }}</strong></td>
        </tr>
    </table>
    <BR>
    <h4>A. KETERANGAN SUB KLASIFIKASI ASET</h4>
    <div class="matik-list" style="font-size:0.9em">
        <ul>
            <li><strong>{{ optional($aset->subklasifikasiaset)->subklasifikasiaset ?? '-' }}</strong> :
                {{ optional($aset->subklasifikasiaset)->penjelasan ?? '-' }}</li>

        </ul>
    </div> <BR>
    <h4>B. KETERANGAN NILAI ASET</h4>
    <ol>
        @foreach ($ranges as $range)
            <li><b>{{ $range->nilai_akhir_aset }} :</b> {{ $range->deskripsi }}</li>
        @endforeach
    </ol><BR>
    <h4>C. CATATAN LAIN</h4>
    <ol>
        <li>Kode TLP (Traffic Light Protocol) dipakai untuk mengklasifikasikan sensitifitas informasi, supaya jelas
            sejauh mana informasi boleh dibagikan.
            Kode TLP:AMBER+STRICT berarti berisi informasi cukup sensitif. Hanya untuk internal organisasi penerima,
            tidak boleh
            keluar.
        </li>
        <li>PERISAI adalah sistem elektronik untuk melakukan <b>PE</b>ngelolaan <b>RIS</b>iko <b>A</b>set
            <b>I</b>nformasi di lingkup Pemerintah Provinsi Bali. PERISAI dikelola oleh
            Dinas Kominfos Provinsi Bali (Contact: Bidang Persandian)
        </li>
        {{-- <li>Aset dalam PERISAI adalah <strong>ASET INFORMASI yang mendukung kinerja organisasi dalam menjalakan proses
                bisnis/layanannya.</strong>
        </li> --}}
        <li>Semua informasi tentang aset ini dapat berubah sesuai dengan review dan pemutahiran data PERISAI yang
            dilakukan minimal sekali setahun oleh Pemilik Aset. Pemutahiran akan dilakukan serempak, menunggu
            informasi dari Diskominfos Prov Bali. </li>
    </ol>

</body>

</html>
