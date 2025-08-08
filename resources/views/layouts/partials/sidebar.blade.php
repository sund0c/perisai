@php
    $role = auth()->user()->role;
@endphp

<aside class="w-64 bg-white border-r shadow-lg">
    <div class="p-4 font-bold text-xl text-blue-600 border-b">
        MATIK
    </div>

    <nav class="mt-4 space-y-2 p-4 text-sm font-medium">
        <a href="{{ route('dashboard') }}" class="block px-2 py-1 rounded hover:bg-blue-100">Dashboard</a>

        @if ($role === 'opd')
            <a href="" class="block px-2 py-1 rounded hover:bg-blue-100">Aset</a>
        @elseif ($role === 'admin')
            <a href="{{ route('opd.index') }}" class="block px-2 py-1 rounded hover:bg-blue-100">OPD</a>
            <a href="" class="block px-2 py-1 rounded hover:bg-blue-100">Pengguna</a>
        @endif
    </nav>
</aside>
