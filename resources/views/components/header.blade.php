<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Sidebar toggle button -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
  </ul>

  <!-- Right navbar -->
  <ul class="navbar-nav ms-auto">
    <li class="nav-item">
      <span class="nav-link">Hi, {{ Auth::user()->name ?? 'Guest' }}</span>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{ route('logout') }}"
         onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
         <i class="fas fa-sign-out-alt"></i> Logout
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
      </form>
    </li>
  </ul>
</nav>
