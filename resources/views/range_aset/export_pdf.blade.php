<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export PDF Range Aset</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        table th, table td {
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
    <h2>Daftar Range Aset</h2>

    <table>
        <thead>
            <tr>
<th class="f12" style="width: 15%;">NILAI ASET</th>
<th class="f12" style="width: 15%;">WARNA (#)</th>
<th class="f12" style="width: 15%;">BATAS</th>
<th class="f12" style="width: auto">KETERANGAN</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($rangeAsets as $item)
                <tr>
                    <td class="f12">{{ $item->nilai_akhir_aset }}</td>
                    <td class="f12">
                        {{ $item->warna_hexa }} 
                        <span style="background-color: {{ $item->warna_hexa }}; display:inline-block; width:20px; height:10px;"></span>
                    </td>
<td class="font-dejavu">{{ number_format($item->nilai_bawah, 0, ',', '.') }} ≤ X ≤ {{ number_format($item->nilai_atas, 0, ',', '.') }}</td>
                    {{-- <td>{{ number_format($item->nilai_atas, 0, ',', '.') }}</td> --}}
                    <td class="f12">{{ $item->deskripsi }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
