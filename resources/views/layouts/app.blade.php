<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>GERES-CENADI — @yield('title','Accueil')</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

{{-- @vite(['resources/css/app.css','resources/js/app.js']) --}}

<style>
/* ===== GLOBAL ===== */
body {
    margin: 0;
    background: #f4f7fb;
    font-family: 'Segoe UI', sans-serif;
}
.app { display: flex; }

/* ===== SIDEBAR ===== */
.sidebar {
    width: 270px;
    min-height: 100vh;
    background: #0096C7;
    color: white;
    padding: 15px;
    display: flex;
    flex-direction: column;
}
.sidebar .brand {
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 20px;
}
.sidebar .nav-link {
    color: rgba(255,255,255,0.8);
    border-radius: 10px;
    padding: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.sidebar .nav-link:hover,
.sidebar .nav-link.active {
    background: rgba(255,255,255,0.15);
    color: white;
}
.sidebar .dropdown-menu {
    background: #00B4D8;
    border: none;
}
.sidebar-bottom { margin-top: auto; }

/* ===== MAIN ===== */
.main { flex: 1; padding: 20px; }
.topbar {
    background: white;
    border-radius: 12px;
    padding: 10px 20px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.badge-disponible { background:#198754; }
.badge-occupe     { background:#dc3545; }
.badge-en-attente { background:#ffc107; color:#000; }
</style>

@stack('styles')
</head>

<body>
<div class="app">

    <!-- SIDEBAR -->
    <div class="sidebar">

        <!-- LOGO -->
        <div class="brand">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>GERES-CENADI
        </div>

        <!-- MENU -->
        <ul class="nav flex-column">

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('ressources.*') ? 'active' : '' }}"
                   href="{{ route('ressources.index') }}">
                    <i class="bi bi-box-seam"></i> Ressources
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}"
                   href="{{ route('reservations.index') }}">
                    <i class="bi bi-calendar-check"></i> Réservations
                </a>
            </li>

            @can('voir statistiques')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('statistiques.*') ? 'active' : '' }}"
                   href="{{ route('statistiques.index') }}">
                    <i class="bi bi-bar-chart-line"></i> Statistiques
                </a>
            </li>
            @endcan

            @can('gerer licences')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('licences.*') ? 'active' : '' }}"
                   href="{{ route('licences.index') }}">
                    <i class="bi bi-key"></i> Licences
                </a>
            </li>
            @endcan

            {{-- @can('voir personnel')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('personnels.*') ? 'active' : '' }}"
                   href="{{ route('personnels.index') }}">
                    <i class="bi bi-people"></i> Personnel
                </a>
            </li>
            @endcan --}}

            {{-- ── PARAMÈTRES DROPDOWN ──────────────────────── --}}
            @can('gerer divisions')
            <li class="nav-item dropdown">
                <button class="nav-link dropdown-toggle btn btn-link {{ request()->is('parametres*') || request()->routeIs('personnels.*') ? 'active border-bottom border-warning' : '' }}"
                        type="button"
                        id="dropdownParametres"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <i class="bi bi-gear me-1"></i> Paramètres
                </button>

                <ul class="dropdown-menu shadow" aria-labelledby="dropdownParametres">

                    <li>
                        <h6 class="dropdown-header text-uppercase"
                            style="color:#1F3864; font-size:11px; letter-spacing:1px;">
                            <i class="bi bi-building me-1"></i> Organisation
                        </h6>
                    </li>

                    <li>
                        <a class="dropdown-item {{ request()->is('parametres*') && request('tab', 'divisions') === 'divisions' ? 'active' : '' }}"
                           href="{{ route('parametres.index', ['tab' => 'divisions']) }}">
                            <i class="bi bi-diagram-3 me-2" style="color:#2E75B6"></i>
                            Divisions
                            <span class="badge bg-secondary float-end" style="font-size:10px">
                                {{ \App\Models\Division::count() }}
                            </span>
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item {{ request()->is('parametres*') && request('tab') === 'services' ? 'active' : '' }}"
                           href="{{ route('parametres.index', ['tab' => 'services']) }}">
                            <i class="bi bi-diagram-2 me-2" style="color:#2E75B6"></i>
                            Services
                            <span class="badge bg-secondary float-end" style="font-size:10px">
                                {{ \App\Models\Service::count() }}
                            </span>
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item {{ request()->is('parametres*') && request('tab') === 'bureaux' ? 'active' : '' }}"
                           href="{{ route('parametres.index', ['tab' => 'bureaux']) }}">
                            <i class="bi bi-door-open me-2" style="color:#2E75B6"></i>
                            Bureaux
                            <span class="badge bg-secondary float-end" style="font-size:10px">
                                {{ \App\Models\Bureau::count() }}
                            </span>
                        </a>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <h6 class="dropdown-header text-uppercase"
                            style="color:#1F3864; font-size:11px; letter-spacing:1px;">
                            <i class="bi bi-people me-1"></i> Utilisateurs
                        </h6>
                    </li>

                    @can('voir personnel')
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('personnels.*') ? 'active' : '' }}"
                           href="{{ route('personnels.index') }}">
                            <i class="bi bi-person-lines-fill me-2" style="color:#2E75B6"></i>
                            Personnel
                            <span class="badge bg-secondary float-end" style="font-size:10px">
                                {{ \App\Models\Personnel::count() }}
                            </span>
                        </a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcan

        </ul>
        {{-- ✅ FIN DU ul.nav — correctement fermé ici --}}

        <!-- BAS SIDEBAR -->
        <div class="sidebar-bottom">

            <a class="nav-link" href="{{ route('bons.index') }}">
                <i class="bi bi-file-earmark-text"></i> Bons
            </a>

            <a class="nav-link position-relative" href="{{ route('notifications.index') }}">
                <i class="bi bi-bell"></i> Notifications
                @php $nbNotifs = auth()->user()->notificationsNonLues()->count(); @endphp
                @if($nbNotifs > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge bg-danger">
                    {{ $nbNotifs > 99 ? '99+' : $nbNotifs }}
                </span>
                @endif
            </a>

            <div class="dropdown mt-3">
                <a class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                    @if(auth()->user()->personnel?->photo)
                        <img src="{{ Storage::url(auth()->user()->personnel->photo) }}"
                             class="rounded-circle me-2" width="28">
                    @else
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center me-2"
                             style="width:28px;height:28px;font-size:11px;font-weight:700">
                            {{ auth()->user()->personnel?->initiales ?? substr(auth()->user()->name,0,2) }}
                        </div>
                    @endif
                    <div>
                        <div style="font-size:13px">
                            {{ auth()->user()->personnel?->prenom ?? auth()->user()->name }}
                        </div>
                        <small class="text-warning">
                            {{ auth()->user()->getRoleNames()->first() }}
                        </small>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <li><div class="dropdown-header">{{ auth()->user()->matricule }}</div></li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mon profil</a></li>
                    <li><hr></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger">Déconnexion</button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="topbar">
            <strong>@yield('title')</strong>
        </div>
        @yield('content')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

</body>
</html>