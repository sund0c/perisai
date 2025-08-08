@extends('adminlte::page')

@section('title', 'Range Kategori SE')

@section('content_header')
    <h1>Range Kategori SE</h1>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
        </span>
    </li>
@endsection


@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex mb-3" style="gap: 10px;">
                <a href="{{ route('rangese.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Range
                </a>
                <a href="{{ route('rangese.export.pdf') }}" target="_blank" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>

            <table id="rangeseTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th>Nilai Aset</th>
                        {{-- <th>Warna</th> --}}
                        <th>Batasan</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        use App\Helpers\ColorHelper;
                    @endphp
                    @foreach ($rangeSes as $item)
                        <tr>
                            {{-- <td>{{ $item->nilai_akhir_aset }}</td> --}}
                            <td
                                style="background-color: {{ $item->warna_hexa }}; color: {{ ColorHelper::getTextColor($item->warna_hexa) }};">
                                {{ $item->nilai_akhir_aset }}
                            </td>

                            {{-- <td><span style="color: {{ $item->warna_hexa }}">{{ $item->warna_hexa }}</span></td> --}}
                            {{-- <td>{{ number_format($item->nilai_bawah, 0) }}</td>
                <td>{{ number_format($item->nilai_atas, 0) }}</td> --}}
                            <td class="font-dejavu">{{ number_format($item->nilai_bawah, 0, ',', '.') }} ≤ X ≤
                                {{ number_format($item->nilai_atas, 0, ',', '.') }}</td>
                            {{-- <td>{{ (int) $item->nilai_atas }}</td> --}}
                            <td>{{ $item->deskripsi }}</td>
                            <td>

                                <div class="d-flex" style="gap: 5px;">

                                    <a href="{{ route('rangese.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('rangese.destroy', $item->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endsection

        @section('js')
            <script>
                $(function() {
                    $('#rangeseTable').DataTable({
                        autoWidth: false,
                        columnDefs: [{
                                width: "100px",
                                targets: 0
                            },
                            {
                                width: "100px",
                                targets: 1
                            },
                            {
                                width: "auto",
                                targets: 2
                            },
                            {
                                width: "100px",
                                targets: 3
                            }
                        ]
                    });
                });
            </script>
        @endsection
