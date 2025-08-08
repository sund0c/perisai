<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Aspek</title>
    <style>
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
    </style>
</head>

<body>
    <h2>Daftar Indikator Keamanan Informasi</h2>
    <h4>Kepgub Bali No. 584/03-E/HK/2024 Pedoman Manajemen Keamanan Informasi</h4>
    <hr>
    <h4>Kategori {{ $fungsiStandar->kategori->nama ?? '-' }}</h4>
    <h4>Aspek {{ $fungsiStandar->nama }}</h4> <BR>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">Urutan</th>
                <th>Indikator</th>
                <th>Tujuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($indikators as $i => $indikator)
                <tr>
                    <td align="center" style="text-align: center;">{{ $indikator->urutan }}</td>
                    <td>
                        {{ $indikator->indikator }}
                    </td>
                    <td>{{ $indikator->tujuan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Belum ada indikator.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
