@extends('adminlte::page')

@section('title', 'Klasifikasi Aset')

@section('content_header')
    <h1>Tahun Aktif</h1>
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
            <a href="{{ route('periodes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Periode
            </a>
        </div>

    <table id="periodesTable" class="table table-bordered table-hover" style="width:100%; table-layout: fixed;">  
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Tampil</th>
                <th>Kunci</th>
                <th width="220">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($periodes as $periode)
                <tr>
                    <td>{{ $periode->tahun }}</td>
                    <td>
                        <span class="badge bg-{{ $periode->status == 'open' ? 'success' : 'secondary' }}">
                            {{ strtoupper($periode->status) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $periode->kunci == 'open' ? 'success' : 'danger' }}">
                            {{ strtoupper($periode->kunci) }}
                        </span>
                    </td>                    
                    <td>

 <div class="d-flex" style="gap: 5px;">
                        
                        
                        <a href="{{ route('periodes.edit', $periode) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('periodes.destroy', $periode) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                         @if ($periode->status !== 'open')
                        <form action="{{ route('periodes.activate', $periode) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm" title="Buka Tahun"><i class="fas fa-check-circle"></i></button>
                        </form>
                        @endif
                         </div>



                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    </div>
@endsection

@section('js')
<script>
    $(function () {

        $('#periodesTable').DataTable({
            autoWidth: false,
            columnDefs: [
                { width: "100px", targets: 0 },
                { width: "100px", targets: 1 },
                { width: "auto", targets: 2 },                
                { width: "100px", targets: 3 }
            ]
        });
    });
</script>
@endsection

