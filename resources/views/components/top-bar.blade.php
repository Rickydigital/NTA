<!-- Topbar Start -->
<div class="navbar-custom shadow-sm">

    <div class="topbar-left-area">
        <button class="button-menu-mobile waves-effect" type="button">
            <i class="mdi mdi-menu"></i>
        </button>

        <a href="{{ route('dashboard') }}" class="topbar-logo-sm ml-2">
            <img src="{{ asset('app-assets/images/logo-sm.png') }}" height="32" alt="Logo">
        </a>

        <div class="topbar-page-info ml-3">
            <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
            <small>@yield('page-subtitle', 'Welcome back')</small>
        </div>
    </div>

    <div class="topbar-right-area">

        {{--  <form class="app-search d-none d-lg-block">
            <div class="input-group">
                <input type="text"
                       class="form-control"
                       placeholder="Search users, students, classes...">
                <div class="input-group-append">
                    <button class="btn btn-light" type="submit">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </div>
            </div>
        </form>  --}}

        <div class="topbar-divider d-none d-lg-block"></div>

        <div class="dropdown notification-list">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
                <i class="mdi mdi-bell-outline" style="font-size:20px;"></i>
                {{--  <span class="badge badge-danger badge-pill noti-icon-badge">3</span>  --}}
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                <div class="dropdown-header">Notifications</div>
                <div class="px-3 py-2 text-muted small">No new notifications</div>
            </div>
        </div>

        <div class="topbar-divider d-none d-md-block"></div>

        @php
            $user = Auth::user();
            $displayName = $user->full_name ?? $user->first_name ?? 'User';
            $nameParts = explode(' ', trim($displayName));
            $initials = strtoupper(substr($nameParts[0], 0, 1));
            if (count($nameParts) > 1) {
                $initials .= strtoupper(substr(end($nameParts), 0, 1));
            }
        @endphp

        <div class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user" data-toggle="dropdown" href="javascript:void(0);">
                <span class="avatar-initials">{{ $initials }}</span>

                <span class="user-meta d-none d-xl-flex">
                    <span class="user-name">{{ $displayName }}</span>
                    <span class="user-role">{{ $user->roles->first()?->name ?? 'No Role' }}</span>
                </span>

                <i class="mdi mdi-chevron-down ml-1 d-none d-xl-inline-block"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                <div class="dropdown-header text-center">
                    <strong>{{ $displayName }}</strong><br>
                    <small class="text-muted">{{ $user->roles->first()?->name ?? 'No Role' }}</small>
                </div>

                <div class="dropdown-divider"></div>

                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="mdi mdi-account-outline mr-2"></i> My Profile
                </a>

                <a href="{{ route('dashboard') }}" class="dropdown-item">
                    <i class="mdi mdi-view-dashboard-outline mr-2"></i> Dashboard
                </a>

                <div class="dropdown-divider"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="mdi mdi-logout mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
<!-- Topbar End -->