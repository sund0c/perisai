<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>PTKKA - On Progress</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">Daftar Aset PTKKA Yang On-Progress</h2>
    <h4 style="text-align: center;">data diekspor Tgl. {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</h4>

    <table>
        <thead>
            <tr>
                <th>Kode Aset</th>
                <th>Nama Aset</th>
                <th>Pemilik Aset</th>
                <th>Tgl Mulai Proses</th>
                <th>Kepatuhan Sementara</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($asetsProses as $aset)
                @php($s = $aset->ptkkaTerakhir ?? null)
                <tr>
                    <td>{{ $aset->kode_aset }}</td>
                    <td>{{ $aset->nama_aset }}</td>
                    <td>{{ $aset->opd->namaopd ?? '-' }}</td>
                    <td>{{ optional($s?->updated_at)->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>
                        {{ $s->kategori_kepatuhan ?? '-' }}
                        @if (isset($s->persentase))
                            ({{ $s->persentase }}%)
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
