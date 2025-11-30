@extends('adminlte::page')

@section('title', 'Daftar OPD')

@section('content_header')
    <h1 class="mb-3">Daftar OPD/UPTD</h1>
@endsection

@section('content_top_nav_left')
    <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">
            Tahun Aktif: {{ $tahunAktifGlobal ?? '-' }}
            @if (($kunci ?? 'locked') === 'locked')
                <i class="fas fa-lock text-danger ml-1" title="Terkunci"></i>
            @else
                <i class="fas fa-lock-open text-success ml-1" title="Terbuka"></i>
            @endif
        </span>
    </li>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        {{-- Tabel --}}
        <table id="opdTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>OPD/UPTD</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no=1;
                @endphp
                @foreach ($opds as $opd)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td><a href="{{ route('bidang.opd.view', $opd->id) }}" class="btn btn-sm btn-warning">
                                {{ $opd->namaopd }}
                            </a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection

@section('js')
    <script>
        $(function() {

            $('#opdTable').DataTable({
                autoWidth: true,
                pageLength: 100,
                stateSave: true,
                columnDefs: [{
                        width: auto,
                        targets: 0
                    },
                    {
                        width: auto,
                        targets: 1
                    }
                ]
            });
        });
    </script>
@endsection
