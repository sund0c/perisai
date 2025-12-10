<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Aset Informasi</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0px;
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

        h2,
        h3,
        h4,
        {
        margin: 0;
        padding: 0;
        }

       ol {
            margin-top: 0;
            padding-top: 0;
            margin-bottom: 0;
            padding-bottom: 0;
            font-size: 12px;
        }

        body {
            font-family: sans-serif;
            font-size: 14px;
        }

       td {
            border: 1px solid #999;
            padding: 6px;
            vertical-align: top;
        }

        .label {
            width: 20%;
            /* font-weight: bold; */
            background-color: #f5f5f5;
        }

        .value {
            width: 60%;
        }

        @page {
            margin: 110px 50px 50px 50px;
        }


        .header {
            position: fixed;
            top: -90px;
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

    </style>
</head>

<body>
    <div class="header">
        <table width="100%" style="margin:0;padding:0;">
            <tr>
                <!-- KIRI: judul + subs (tetap) -->
                <td style="vertical-align: top;border:none;">
                    <h2 style="margin-top:40px;">PROFIL ASET INFORMASI :: Tahun {{ $tahunAktifGlobal ?? '-' }}
                    </h2>

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
                                        ? 'Bidang/Bagian'
                                        : ($field === 'unit_personil'
                                            ? 'Seksi/Tim'
                                            : ucwords(str_replace('_', ' ', $field)))))));

  if ($field === 'subklasifikasiaset_id') { $value = ($aset->subklasifikasiaset->subklasifikasiaset ?? '-') ; 
  $k = '<br><small>' . (optional($aset->subklasifikasiaset)->penjelasan ?? '-') . '</small>'; } 
  else { $value = $aset->$field ?? '-';  $k = null;}

                if (in_array($field, ['link_url', 'link_pse']) && $value === '-') {
                    $value = '';
                }

               
                // Daftar field CIAAA + kode singkat
                $ciaaaMap = [
                    'kerahasiaan' => 'C',
                    'integritas' => 'I',
                    'ketersediaan' => 'A',
                    'keaslian' => 'A',
                    'kenirsangkalan' => 'N',
                ];

                $labels = [
                    1 => 'Rendah',
                    2 => 'Sedang',
                    3 => 'Tinggi',
                ];
                $isLinkField = in_array($field, ['link_url', 'link_pse']) && filter_var($value, FILTER_VALIDATE_URL);
                $displayUrl = $value;
                if ($isLinkField) {
                    $displayUrl = preg_replace('#^https?://#i', '', $value);
                }
            @endphp

            {{-- Lewati field keaslian dan kenirsangkalan sepenuhnya --}}
            @unless (in_array($field, ['kenirsangkalan', 'keaslian']))
                <tr>
                    <td class="label">
                        @if (array_key_exists($field, $ciaaaMap))
                            Tingkat {{ $label }} ({{ $ciaaaMap[$field] }})
                        @else
                            {{ $label }}
                        @endif
                    </td>
                    <td class="value">
                        @if ($isLinkField)
                            <strong><a href="{{ $value }}" target="_blank" rel="noopener"
                                style="text-decoration:none;color:#000;">{{ $displayUrl }}</a></strong>
                        @else
                            <strong>{{ $labels[$value] ?? ($value === '' ? '-' : $value) }} </strong>
                            {!! $k !!}

                            
                        @endif
                    </td>
                </tr>
            @endunless
        @endforeach

        {{-- Baris terakhir --}}
        <tr>
            <td class="label">Nilai Kritikal Aset</td>
            <td class="value"><strong>{{ $aset->nilai_akhir_aset }}</strong><BR>
             @php
        $deskripsi = $ranges->firstWhere('nilai_akhir_aset', $aset->nilai_akhir_aset)->deskripsi ?? null;
    @endphp

    <small>{{ $deskripsi }}</small>
</td>
        </tr>
                <tr>
                    <td class="label">Pemilik Aset</td>
                    <td class="value"><strong>{{ strtoupper($namaOpd) }}</strong><br><small>
                        Pemilik Aset bertanggung jawab terhadap proses bisnis/layanannya, pengelolaan aset informasi, pengukuran nilai aset, 
                        klasifikasi aset, kategorisasi Sistem Elektronik, penilaian vitalitas, penilaian kepatuhan, 
                        pemetaan risiko, analisis risiko, serta penyusunan dan implementasi mitigasi risiko.</small></td>
        </tr>
                <tr>
                    <td class="label">Pemilik Risiko</td>
                    <td class="value"><strong>KEPALA {{ $namaOpd }}</strong><BR><small>
                        Pemilik Risiko bertanggung jawab untuk menyetujui rencana mitigasi risiko, menetapkan tingkat risiko yang 
                        dapat diterima (acceptable risk), menyetujui residual risk, serta memastikan dukungan sumber daya yang diperlukan.</small></td>
        </tr>
    </table>

    <BR>

    </div> 

    <h4>CATATAN</h4>
    <ol>
        <li>Dokumen ini menggunakan Kode TLP:AMBER+STRICT mengindikasikan mengandung informasi yang cukup sensitif.
            Informasi di dalam dokumen ini hanya boleh didistribusikan kepada Pemilik Aset dan Dinas Kominfos Prov Bali. 
            Tidak boleh diberikan secara langsung dan atau otomatis ke pihak lain.
        </li>
        <li>PERISAI adalah sistem elektronik untuk membantu pengelolaan risiko aset informasi di lingkup Pemprov Bali 
            agar menjadi lebih efektif dengan mengusung konsep RISE : Recognise.Identify.Secure.Enhanced. PERISAI dikelola oleh Bidang Persandian Dinas Kominfos Provinsi Bali.
        </li>
        <li>Profil Aset Informasi dimutahirkan setahun sekali</li>
    </ol>
</body>

</html>
