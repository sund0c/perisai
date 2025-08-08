<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Export PDF Indikator Kategori SE</title>
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
    </style>
</head>

<body>
    <h2>Indikator Kategori SE</h2>

    <table>
        <thead>
            <tr>
                <th class="f12" style="width: 10%;">KODE</th>
                <th class="f12" style="width: auto%;">PERTANYAAN</th>
                <th class="f12" style="width: auto%;">OPSI A</th>
                <th class="f12" style="width: auto%;">OPSI B</th>
                <th class="f12" style="width: auto">OPSI C</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($indikator as $item)
                <tr>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->pertanyaan }}</td>
                    <td class="f12">{{ $item->opsi_a }} ({{ $item->nilai_a }})</td>
                    <td class="f12">{{ $item->opsi_b }} ({{ $item->nilai_b }})</td>
                    <td class="f12">{{ $item->opsi_c }} ({{ $item->nilai_c }})</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
