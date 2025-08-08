<aside x-data="{ open: true }" class="bg-gray-900 text-white h-screen transition-all duration-300"
    :class="{ 'w-64': open, 'w-20': !open }">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-700">
        <div class="flex items-center space-x-2">
            <i class="fas fa-cogs text-lg text-white"></i>
            <span x-show="open" class="text-lg font-semibold">MATIK</span>
        </div>
        <button @click="open = !open" class="text-gray-400 hover:text-white focus:outline-none">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-4">
        <ul class="space-y-1 px-2">
            <li>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center space-x-3 px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('dashboard') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span x-show="open">Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('rangeaset.index') }}"
                    class="flex items-center space-x-3 px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('rangeaset.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-layer-group"></i>
                    <span x-show="open">Range Aset</span>
                </a>
            </li>

            <li>
                <a href="{{ route('periodes.index') }}"
                    class="flex items-center space-x-3 px-3 py-2 rounded hover:bg-gray-800 {{ request()->routeIs('periodes.*') ? 'bg-gray-800' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span x-show="open">Periode</span>
                </a>
            </li>

            <li>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="flex items-center space-x-3 px-3 py-2 rounded hover:bg-gray-800">
                    <i class="fas fa-sign-out-alt"></i>
                    <span x-show="open">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </li>
        </ul>
    </nav>
</aside>
