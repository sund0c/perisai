@extends('adminlte::page')

@section('title', 'Backup Database')

@section('content_header')
    <h1>Backup Database Terbaru</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nama File</th>
                        <th>Ukuran (KB)</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($files as $file)
                        <tr>
                            <td>{{ $file['name'] }}</td>
                            <td>{{ $file['size'] }}</td>
                            <td>{{ $file['date'] }}</td>
                            <td>
                                <a href="{{ route('admin.backup.download', $file['name']) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i> Unduh
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
@stop
