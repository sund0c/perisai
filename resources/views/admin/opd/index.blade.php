@extends('adminlte::page')

@section('title', 'Daftar OPD')

@section('content_header')
    <h1 class="mb-3">Daftar OPD</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">

    {{-- Tombol Aksi --}}
        <div class="mb-4">
            <a href="{{ route('opd.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Tambah OPD
            </a>
            <button id="exportCSV" class="btn btn-sm btn-success">
                <i class="fas fa-file-csv"></i> Export CSV
            </button>
            <button id="exportPDF" class="btn btn-sm btn-danger">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>

        {{-- Tabel --}}
        <table id="opdTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama OPD</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($opds as $opd)
                    <tr>
                        <td>{{ $opd->id }}</td>
                        <td>{{ $opd->namaopd }}</td>
                        <td>
                            <a href="{{ route('opd.edit', $opd->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('opd.destroy', $opd->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus OPD ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection

@section('js')
<!-- Tambahkan CDN lengkap untuk export -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- Plugin export -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function () {
        const table = $('#opdTable').DataTable({
            ordering: true,
            stateSave: true,
            columnDefs: [
                { orderable: false, targets: [0, 2] }
            ],
            dom: 'frtip',
            buttons: [
                {
                    extend: 'csvHtml5',
                    title: 'Daftar_OPD',
                    exportOptions: { columns: [1] },
                    className: 'buttons-csv'
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Daftar OPD',
                    exportOptions: { columns: [1] },
                    className: 'buttons-pdf'
                }
            ]
        });

        // Trigger tombol custom
        $('#exportCSV').click(() => table.button('.buttons-csv').trigger());
        $('#exportPDF').click(() => table.button('.buttons-pdf').trigger());
    });
</script>
@endsection
