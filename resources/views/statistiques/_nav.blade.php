{{-- Inclusion dans chaque vue stats : @include('statistiques._nav') --}}

<div class="nav nav-pills mb-4 gap-1 flex-wrap">

    @foreach([
        ['statistiques.index',       'Globales',      'bi-globe'],
        ['statistiques.ressources',  'Ressources',    'bi-box-seam'],
        ['statistiques.divisions',   'Divisions',     'bi-diagram-3'],
        ['statistiques.reservations','Réservations',  'bi-calendar-check'],
        ['statistiques.temporelles', 'Temporelles',   'bi-clock-history'],
        ['statistiques.utilisateurs','Utilisateurs',  'bi-people'],
        ['statistiques.licences',    'Licences',      'bi-key'],
        ['statistiques.ia',          'Module d\'aide à la décision',     'bi-robot'],
    ] as [$route, $label, $icon])

        <a href="{{ route($route) }}"
           class="nav-link py-1 px-3 {{ request()->routeIs($route) ? 'active' : 'bg-white border text-dark' }}">
            <i class="{{ $icon }} me-1"></i>
            {{ $label }}
        </a>

    @endforeach

</div>