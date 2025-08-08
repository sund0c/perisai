<!DOCTYPE html>
<html>
<head>
    <title>Export PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }
        th {
            background-color: #f0f0f0;
            text-align: center;
        }
    </style>
</head>
<body>
    <h3 style="text-align: center;">Definisi Sub Klasifikasi Aset</h3>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Aspek</th>
                <th>Sub Klasifikasi Aset</th>
                <th>Penjelasan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($klasifikasis as $klasifikasi)
                @foreach($klasifikasi->subklasifikasi as $index => $sub)
                    <tr>
                        @if($index == 0)
                        <td rowspan="{{ count($klasifikasi->subklasifikasi) }}">{{ $no++ }}</td>
                        <td rowspan="{{ count($klasifikasi->subklasifikasi) }}">{{ $klasifikasi->klasifikasiaset }}</td>
                        @endif
                        <td>{{ $sub->subklasifikasiaset }}</td>
                        <td>{{ $sub->penjelasan }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
