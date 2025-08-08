<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MATIK</title>

    {{-- CSS utama dari Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- DataTables CSS dari CDN --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
</head>
<body class="bg-gray-100 text-gray-900">

    {{-- Navigasi dan sidebar --}}
    @include('layouts.navigation')
    <div class="flex min-h-screen">
        @include('layouts.partials.sidebar')

        {{-- Konten utama --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    {{-- jQuery CDN --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Penting: Override jQuery global agar tidak bentrok
        window.$ = window.jQuery = jQuery;
    </script>

    {{-- DataTables CDN --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>



    {{-- Script tambahan dari setiap view --}}


</body>
</html>
