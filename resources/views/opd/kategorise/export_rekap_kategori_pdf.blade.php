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
    <h2>Rekap Kategori SE: {{ strtoupper($kategori) }}, Tahun {{ $tahunAktifGlobal ?? '-' }}</h2>
    <h3>Pemilik Aset : {{ strtoupper($namaOpd) }}</h3>
    <table class="table table-bordered text-center" style="width:100%">
        <thead class="font-weight-bold">
            <tr>
                <th>Nama Aset</th>
                <th>Sub Klasifikasi</th>
                <th>Lokasi</th>
                <th>Skor Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $aset)
                <tr>
                    <td>{{ $aset->nama_aset }}</td>
                    <td>{{ $aset->subklasifikasiaset->subklasifikasiaset ?? '-' }}</td>
                    <td>{{ $aset->lokasi }}</td>
                    <td>
                        {{-- {{ $aset->kategoriSe->skor_total ?? 'BELUM DINILAI' }} --}}
                        @php
                            $skor = $aset->kategoriSe->skor_total ?? null;

                            if ($skor === null) {
                                echo '<span class="badge badge-secondary">BELUM DINILAI</span>';
                            } else {
                                $range = $rangeSes->first(function ($r) use ($skor) {
                                    return $skor >= $r->nilai_bawah && $skor <= $r->nilai_atas;
                                });

                                $warna = $range->warna_hexa ?? '#999';
                                $label = $range->nilai_akhir_aset ?? 'TIDAK DIKETAHUI';

                                echo '<span class="badge" style="background-color: ' .
                                    $warna .
                                    '; color: #fff;">' .
                                    $skor .
                                    ' (' .
                                    strtoupper($label) .
                                    ')</span>';
                            }
                        @endphp
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <BR><BR><BR>
    <h4>Catatan</h4>
    <ol>
        <li><b>PERISAI</b> adalah sistem elektronik untuk melakukan <span class="underline">PEngelolaan RISiko Aset
                Informasi,</span> dikelola oleh
            Bidang
            Persandian Dinas Kominfos Provinsi Bali</li>
        <li>Yang dimaksud dengan <b>Aset</b> dalam PERISAI adalah <span class="underline">khusus aset yang terkait
                dengan pelindungan data dan keamanan informasi.</span>
        </li>
        <li>Periode pemutahiran data PERISAI <b>wajib dilakukan sekali setahun oleh Pemilik Aset.</b> </li>
        <li>SE adalah sistem elektronik yaitu dalam PERISAI adalah <strong>aset dengan klasifikasi [PL] Perangkat
                Lunak.</strong> Contoh SE adalah
            website, aplikasi
            berbasis web, mobile, sistem operasi dan utility.</li>
    </ol>
</body>

</html>
