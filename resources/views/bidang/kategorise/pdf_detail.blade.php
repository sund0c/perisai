<!DOCTYPE html>
<html>

<head>
    <title>Hasil Penilaian Kategori SE</title>
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
        p {
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

        .small {
            font-size: 12px;
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
                    <h2 style="margin-top:40px;">KATEGORISASI SE :: Tahun {{ $tahunAktifGlobal ?? '-' }}
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
        <tr>
            <td class="label">
                Kode Aset
            </td>
            <td class="value">
                <strong>{{ $aset->kode_aset }}</strong>
            </td>
        </tr>
        <tr>
            <td class="label">
                Nama Aset
            </td>
            <td class="value">
                <strong>{{ $aset->nama_aset }}</strong>
            </td>
        </tr>
        <tr>
            <td class="label">
                Pemilik Aset
            </td>
            <td class="value">
                <strong>{{ $aset->opd->namaopd }}</strong>
            </td>
        </tr>
        <tr>
            <td class="label">
                Kategorisasi Aset
            </td>
            <td class="value">
                <strong>{{ strtoupper($kategoriLabel) }}</strong>
                <p class="small" style="margin-bottom: 0">{{ $deskripsiLabel ?? '' }}</p>
            </td>
        </tr>
    </table></br>
    <h4>FORM KATEGORISASI:</h4>
    <table>
        <thead>
            <tr>
                <th style="text-align: center;width:10px">NO</th>
                <th style="text-align: center;width:auto">INDIKATOR</th>
                <th style="text-align: center;width:auto">KETERANGAN</th>
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
                        {{ $jawaban }}.
                        @if ($keterangan)
                            <p class="small">{{ $keterangan }}</p>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table><BR>
    <h4>CATATAN</h4>
    <ol>
        <li>Dokumen ini menggunakan Kode TLP:AMBER+STRICT mengindikasikan mengandung informasi yang cukup sensitif.
            Informasi di dalam dokumen ini hanya boleh didistribusikan kepada Pemilik Aset dan Dinas Kominfos Prov Bali.
            Tidak boleh diberikan secara langsung dan atau otomatis ke pihak lain.
        </li>
        <li>PERISAI adalah sistem elektronik untuk membantu pengelolaan risiko aset informasi di lingkup Pemprov Bali
            agar menjadi lebih efektif dengan mengusung konsep RISE : Recognise.Identify.Secure.Enhanced. PERISAI
            dikelola oleh Bidang Persandian Dinas Kominfos Provinsi Bali.
        </li>
        <li>Profil Aset Informasi dimutahirkan setahun sekali</li>
    </ol>
</body>

</html>
