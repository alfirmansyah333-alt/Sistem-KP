<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <!-- Toggle Button for Desktop -->
    <div class="navbar-nav align-items-center me-3 d-none d-xl-flex">
      <a class="nav-item nav-link layout-menu-toggle px-0 me-xl-4" href="javascript:void(0)">
        <i class="bx bx-menu bx-sm"></i>
      </a>
    </div>

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <li class="nav-item dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="#" data-bs-toggle="dropdown">
          <div class="avatar">
            @if(auth()->user()->profile_photo_path)
              <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt class="w-px-40 rounded-circle" style="height: 40px; width: 40px; object-fit: cover; object-position: center;" />
            @else
              <img src="{{ asset('assets/img/avatars/DefaultProfile.png') }}" alt class="w-px-40 rounded-circle" style="height: 40px; width: 40px; object-fit: cover; object-position: center;" />
            @endif
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
              <i class="bx bx-user me-2"></i> Edit Profil
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="{{ route('profile.change-password') }}">
              <i class="bx bx-lock me-2"></i> Ubah Password
            </a>
          </li>
          <li><div class="dropdown-divider"></div></li>
          <li>
            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-navbar').submit();">
              <i class="bx bx-log-out me-2"></i> Log Out
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </div>

  @if(auth()->check())
  <form id="logout-form-navbar" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
  </form>
  @endif
</nav>
