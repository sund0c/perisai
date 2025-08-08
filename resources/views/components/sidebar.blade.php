<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="#" class="brand-link">
    <span class="brand-text font-weight-light">MATIK</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
        <li class="nav-item">
          <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('klasifikasiaset.index') }}" class="nav-link">
            <i class="nav-icon fas fa-layer-group"></i>
            <p>Klasifikasi Aset</p>
          </a>
        </li>

        <!-- Tambah menu lain sesuai kebutuhan -->
      </ul>
    </nav>
  </div>
</aside>
