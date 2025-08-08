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
    <h2>Rekomendasi Keamanan Informasi</h2>
    <h4>Kepgub Bali No. 584/03-E/HK/2024 Pedoman Manajemen Keamanan Informasi</h4>
    <hr>
    <h4>Kategori {{ $fungsiStandar->kategori->nama ?? '-' }}</h4>
    <h4>Aspek {{ $fungsiStandar->nama }} </h4>
    <h4>Indikator "{{ $indikator->indikator }}"</h4><br>
    <table>
        <thead class="thead-light">
            <tr>
                <th width="20px">#</th>
                <th>Rekomendasi</th>
                <th width="300px">Bukti Dukung</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekomendasis as $index => $rek)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td>{{ $rek->rekomendasi }}</td>
                    <td>{{ $rek->buktidukung }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada rekomendasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
