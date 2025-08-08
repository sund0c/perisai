<!DOCTYPE html>
<html>

<head>
    <title>Export PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
            text-align: center;
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
    </style>
</head>

<body>
    <h3">Sub Klasifikasi Aset</h3>
        <h4>Klasifikasi Aset : {{ $klasifikasis->klasifikasiaset }}</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width:30px">No</th>
                        <th style="width:250px">Sub Klasifikasi Aset</th>
                        <th style="width:autopx">Penjelasan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($subklasifikasi as $sub)
                        <tr>
                            <td style="text-align: center">{{ $no++ }}</td>
                            <td>{{ $sub->subklasifikasiaset }}</td>
                            <td>{{ $sub->penjelasan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
</body>

</html>
