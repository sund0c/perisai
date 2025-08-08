<header class="bg-white shadow px-6 py-4 flex justify-between items-center border-b border-gray-200">
    <!-- Burger icon -->
    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-black focus:outline-none text-xl">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Tahun Aktif -->
    @if(isset($tahunAktif))
        <div class="text-blue-600 font-semibold text-sm flex items-center">
            <i class="fas fa-calendar-alt mr-1"></i> PERIODE {{ $tahunAktif->tahun }}
        </div>
    @endif

    <!-- User & Logout -->
    <div class="flex items-center space-x-4 text-sm text-gray-700">
        <span>{{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-red-500 hover:text-red-700">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</header>
