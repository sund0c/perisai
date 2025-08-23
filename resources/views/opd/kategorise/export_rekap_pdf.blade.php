<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Kategori SE</title>
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
        <img src="{{ public_path('images/tlp/tlp_teaser_green.png') }}" alt="TLP:GREEN" width="150">
        {{-- <p style="font-weight:bold; color:#FFBF00; margin:0;">TLP:AMBER+STRICT</p> --}}
    </div>
    <h2>Rekap Kategori SE Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
    <h3>Pemilik Aset : {{ strtoupper($namaOpd) }}</h3>
    <table class="table table-bordered text-center" style="width:100%">
        <thead class="font-weight-bold">
            <tr>
                <th style="width:80%;text-align: center;height: 40px">KATEGORI SISTEM ELEKTRONIK (SE)</th>
                <th style="text-align: center;">JUMLAH ASET</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td
                    style="vertical-align:
                    middle; height: 70px;background-color:#FF0000; color:#fff;text-align: left">
                    <b>TINGGI</b>
                    <div>
                        {{ data_get($kategoriMeta, 'TINGGI.deskripsi', '-') }}
                    </div>
                </td>
                <td style="vertical-align: middle; text-align: center;font-size:.9rem;">
                    {{ $tinggi }}

                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle; height: 70px;background-color:#FFD700; color:#000;text-align: left">
                    <b>SEDANG</b>
                    <div>
                        {{ data_get($kategoriMeta, 'SEDANG.deskripsi', '-') }}
                    </div>
                </td>
                <td style="vertical-align: middle; text-align: center;font-size:.9rem;"> {{ $sedang }}
                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle; height: 70px;background-color:#00B050; color:#fff;text-align: left">
                    <b>RENDAH</b>
                    <div>
                        {{ data_get($kategoriMeta, 'RENDAH.deskripsi', '-') }}
                    </div>
                </td>
                <td style="vertical-align: middle; text-align: center;font-size:.9rem;"> {{ $rendah }}

                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle; height: 70px;bg-secondary text-white;text-align: left">
                    <b>Belum Dinilai</b>
                    <div>
                        {{ data_get($kategoriMeta, 'BELUM.deskripsi', '-') }}
                    </div>
                </td>
                <td style="vertical-align: middle; text-align: center;font-size:.9rem;"> {{ $belum }}

                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="background-color:#eeeeee; font-weight:bold;">
                <td style="text-align: center;height: 40px">TOTAL JUMLAH ASET</td>
                <td style="vertical-align: middle; text-align: center;font-size:.9rem;">{{ $total }}</td>
            </tr>
    </table>
    <BR><BR><BR>
    <h4>Catatan</h4>
    <ol>
        <li>Kode TLP (Traffic Light Protocol) dipakai untuk mengklasifikasikan sensitivitas informasi, supaya jelas
            sejauh mana informasi boleh dibagikan.
            <b>Kode TLP:GREEN berarti boleh dibagikan di dalam komunitas/sektor (misalnya antar instansi pemerintah),
                tapi tidak untuk publik luas.</b>
        </li>
        <li><b>PERISAI</b> adalah sistem elektronik untuk melakukan <span class="underline">PEngelolaan RISiko Aset
                Informasi,</span> dikelola oleh
            Bidang
            Persandian Dinas Kominfos Provinsi Bali</li>
        <li>Yang dimaksud dengan <b>Aset</b> dalam PERISAI adalah <span class="underline">khusus aset yang terkait
                dengan pelindungan data dan keamanan informasi.</span>
        </li>
        <li>Periode pemutahiran data PERISAI <b>wajib dilakukan sekali setahun oleh Pemilik Aset.</b>. Pemutahiran
            dilakukan serentak menunggu informasi dari Dinas Kominfos Prov Bali. </li>
        <li>SE adalah sistem elektronik yaitu dalam PERISAI adalah <strong>aset dengan klasifikasi [PL] Perangkat
                Lunak.</strong> Contoh SE adalah
            website, aplikasi
            berbasis web, mobile, sistem operasi dan utility.</li>
    </ol>
</body>

</html>
