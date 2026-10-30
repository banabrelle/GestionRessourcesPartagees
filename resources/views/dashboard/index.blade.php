@extends('layouts.app')
@section('title', 'Tableau de bord')

@section('content')

<style>

    body{
        background:#BDE0FE;
    }


    .dashboard-header{
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:25px;
        flex-wrap:wrap;
        gap:15px;
    }

    .dashboard-title{
        font-size:34px;
        font-weight:800;
        color:#173b36;
        margin-bottom:5px;
    }

    .dashboard-subtitle{
        color:#5d7771;
        font-size:14px;
    }

    /* ===== CARD ===== */

    .soft-card{
        background:#f6fbf9;
        border-radius:28px;
        border:none;
        box-shadow:
            8px 8px 18px rgba(163,177,198,.18),
            -8px -8px 18px rgba(255,255,255,.9);
    }

    /* ===== STATS ===== */

    .stats-card{
        padding:24px;
        height:100%;
        transition:.25s;
    }

    .stats-card:hover{
        transform:translateY(-3px);
    }

    .stats-icon{
        width:68px;
        height:68px;
        border-radius:22px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:30px;
        margin-bottom:18px;
    }

    .icon-green{background:#dff6eb;color:#1f9d73;}
    .icon-orange{background:#fff2dd;color:#f59e0b;}
    .icon-blue{background:#dff2f6;color:#2094a8;}
    .icon-red{background:#ffe4e4;color:#ef4444;}

    .stats-value{
        font-size:38px;
        font-weight:800;
        color:#173b36;
        line-height:1;
    }

    .stats-label{
        color:#69817b;
        font-size:15px;
        margin-top:5px;
    }

    .stats-bottom{
        margin-top:18px;
        display:flex;
        justify-content:space-between;
        flex-wrap:wrap;
        gap:10px;
        font-size:13px;
    }

    /* ===== TABLE ===== */

    .table-card{padding:0;overflow:hidden;}

    .card-head-modern{
        padding:22px 24px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        border-bottom:1px solid #edf2ef;
    }

    .card-head-modern h5{
        margin:0;
        font-size:21px;
        font-weight:700;
        color:#173b36;
    }

    .table-modern{margin:0;}

    .table-modern thead th{
        border:none;
        padding:18px 22px;
        font-size:12px;
        text-transform:uppercase;
        color:#7b918b;
        background:#f8fcfb;
    }

    .table-modern tbody td{
        border:none;
        padding:18px 22px;
        vertical-align:middle;
    }

    .table-modern tbody tr{
        border-bottom:1px solid #edf3f1;
        transition:.2s;
    }

    .table-modern tbody tr:hover{background:#f3faf8;}

    /* ===== BADGES ===== */

    .badge-soft{padding:8px 12px;border-radius:30px;font-size:12px;font-weight:600;}
    .badge-green{background:#dff6eb;color:#14915f;}
    .badge-orange{background:#fff1d9;color:#d48a09;}
    .badge-red{background:#ffe3e3;color:#d93838;}
    .badge-blue{background:#dff3f7;color:#1e7f92;}

    /* ===== CALENDRIER ===== */

    .calendar-card{padding:0;overflow:hidden;}

    .calendar-header{
        padding:22px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        border-bottom:1px solid #edf2ef;
    }

    .calendar-title{font-size:24px;font-weight:800;color:#173b36;}
    .calendar-body{padding:20px;}

    .calendar-days{
        display:grid;
        grid-template-columns:repeat(7,1fr);
        gap:10px;
    }

    .calendar-day-name{
        text-align:center;
        font-size:13px;
        color:#6e8580;
        font-weight:600;
        margin-bottom:10px;
    }

    .calendar-date{
        aspect-ratio:1/1;
        background:white;
        border-radius:18px;
        display:flex;
        align-items:center;
        justify-content:center;
        position:relative;
        font-weight:700;
        color:#173b36;
        box-shadow:0 4px 10px rgba(0,0,0,0.04);
        transition:.2s;
    }

    .calendar-date:hover{transform:translateY(-2px);}

    .calendar-active{background:linear-gradient(135deg,#149c74,#46c793);color:white;}

    .calendar-dot{
        width:8px;height:8px;border-radius:50%;
        position:absolute;top:10px;right:10px;
    }

    .dot-green{background:#22c55e;}
    .dot-orange{background:#f59e0b;}
    .dot-red{background:#ef4444;}

    /* ===== MODULE IA ===== */

    .ia-card{
        background:linear-gradient(135deg,#16856a,#36c18f);
        color:white;
        border-radius:30px;
        padding:28px;
        position:relative;
        overflow:hidden;
    }

    .ia-card::before{
        content:'';
        position:absolute;
        width:280px;height:280px;
        background:rgba(255,255,255,.05);
        border-radius:50%;
        top:-120px;right:-100px;
    }

    .ia-title{font-size:24px;font-weight:700;margin-bottom:25px;}
    .ia-number{font-size:42px;font-weight:800;}

    /* ===== LICENCES ===== */

    .licence-card{padding:0;overflow:hidden;}

    .licence-item{
        padding:18px 22px;
        border-bottom:1px solid #edf2ef;
    }

    .licence-item:last-child{border-bottom:none;}

    /* ===== BUTTON ===== */

    .btn-modern{
        border:none;border-radius:16px;padding:10px 18px;
        background:#e5f5ef;color:#16856a;font-weight:600;
    }

    .btn-modern:hover{background:#d4efe6;color:#16856a;}

    /* ===== BANNIÈRE PERSONNEL ===== */

    .personnel-banner{
        background:linear-gradient(135deg,#1e7f92,#38b2c8);
        color:white;
        border-radius:24px;
        padding:30px 35px;
        margin-bottom:25px;
        display:flex;
        align-items:center;
        gap:20px;
    }

    .personnel-banner-icon{
        width:70px;height:70px;
        background:rgba(255,255,255,.2);
        border-radius:20px;
        display:flex;align-items:center;justify-content:center;
        font-size:32px;flex-shrink:0;
    }

    .personnel-banner h4{font-size:22px;font-weight:700;margin:0 0 6px;}
    .personnel-banner p{margin:0;opacity:.85;font-size:14px;}

    /* ===== RESPONSIVE ===== */

    @media(max-width:992px){
        .dashboard-title{font-size:28px;}
        .stats-value{font-size:30px;}
        .calendar-date{border-radius:12px;font-size:13px;}
    }

</style>

{{-- ══ HEADER ══════════════════════════════════════════════════ --}}
<div class="dashboard-header">

    <div>
        <div class="dashboard-subtitle">
            Bienvenue,
            {{ auth()->user()->personnel?->prenom ?? auth()->user()->name }}
        </div>

        <div class="dashboard-title">
            Tableau de bord
        </div>

        {{-- Badge division — chef_division uniquement --}}
        @if($divisionCourante)
        <div class="mt-1">
            <span class="badge"
                  style="background:#dff2f6;color:#1e7f92;font-size:13px;padding:6px 14px;border-radius:20px;">
                <i class="bi bi-diagram-3 me-1"></i>
                {{ $divisionCourante->code }} — {{ $divisionCourante->nom }}
            </span>
            <small class="text-muted ms-2" style="font-size:12px;">
                Statistiques de votre division
            </small>
        </div>
        @endif
    </div>

    @canany(['exporter rapports', 'voir statistiques division'])
<div class="dropdown">
 
    <button class="btn btn-modern dropdown-toggle" data-bs-toggle="dropdown">
        <i class="bi bi-download me-2"></i>Exporter
    </button>
 
    <ul class="dropdown-menu border-0 shadow" style="min-width:230px;">
 
        {{-- ── Exports disponibles pour l'admin ── --}}
        @can('exporter rapports')
 
        <li><h6 class="dropdown-header text-muted" style="font-size:10px;">GLOBAL</h6></li>
 
        <li>
            <a class="dropdown-item" href="{{ route('export.reservations-pdf') }}">
                <i class="bi bi-filetype-pdf text-danger me-2"></i>Réservations PDF
            </a>
        </li>
 
        <li>
            <a class="dropdown-item" href="{{ route('export.inventaire-pdf') }}">
                <i class="bi bi-filetype-pdf text-danger me-2"></i>Inventaire PDF
            </a>
        </li>
 
        <li>
            <a class="dropdown-item" href="{{ route('export.statistiques-pdf') }}">
                <i class="bi bi-bar-chart-fill text-primary me-2"></i>Statistiques globales PDF
            </a>
        </li>
 
        <li><hr class="dropdown-divider"></li>
 
        <li><h6 class="dropdown-header text-muted" style="font-size:10px;">PAR DIVISION</h6></li>
 
        {{-- Liste de toutes les divisions pour export individuel --}}
        @foreach(\App\Models\Division::orderBy('code')->get() as $div)
        <li>
            <a class="dropdown-item" href="{{ route('export.statistiques-division-pdf', $div) }}">
                <i class="bi bi-diagram-3 text-info me-2"></i>
                {{ $div->code }} — {{ \Illuminate\Support\Str::limit($div->nom, 25) }}
            </a>
        </li>
        @endforeach
 
        @endcan
 
        {{-- ── Export division pour le chef_division ── --}}
        @if(auth()->user()->hasRole('chef_division') && auth()->user()->personnel?->division)
        @php $maDiv = auth()->user()->personnel->division; @endphp
 
        <li>
            <a class="dropdown-item" href="{{ route('export.statistiques-division-pdf', $maDiv) }}">
                <i class="bi bi-bar-chart-fill text-success me-2"></i>
                Statistiques — {{ $maDiv->code }}
            </a>
        </li>
 
        @endif
 
    </ul>
 
</div>
@endcanany

</div>

{{--
     CAS 1 : SIMPLE PERSONNEL — bannière + ses réservations uniquement
     Pas de stats, pas de licences, pas de module IA
 --}}
@if($estPersonnel)

    {{-- Bannière d'accueil personnalisée --}}
    <div class="personnel-banner">
        <div class="personnel-banner-icon">
            <i class="bi bi-person-check-fill"></i>
        </div>
        <div>
            <h4>Bienvenue sur notre plateforme</h4>
            <p>
                Suivre et plannifier vos réservations .
                Pour réserver une ressource, rendez-vous dans la section
                <a href="{{ route('ressources.index') }}" class="text-white fw-bold">Ressources</a>.
            </p>
        </div>
    </div>

    {{-- Tableau de ses réservations --}}
    <div class="soft-card table-card">

        <div class="card-head-modern">
            <h5>
                <i class="bi bi-clock-history me-2"></i>Mes réservations récentes
            </h5>
            <a href="{{ route('reservations.index') }}" class="btn btn-modern">
                Voir tout
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Ressource</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservationsRecentes as $r)
                    <tr>
                        <td>{{ Str::limit($r->ressource?->nom, 30) }}</td>
                        <td>{{ $r->date_debut?->format('d/m H:i') }}</td>
                        <td>{{ $r->date_fin?->format('d/m H:i') }}</td>
                        <td>
                            <span class="badge bg-{{ $r->badge_color }}">
                                {{ $r->statut_label }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('reservations.show', $r) }}"
                               class="btn btn-light rounded-circle shadow-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                            Aucune réservation pour le moment
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

{{-- ══════════════════════════════════════════════════════════════
     CAS 2 : ADMIN et CHEF_DIVISION — dashboard complet avec stats
══════════════════════════════════════════════════════════════ --}}
@else

    {{-- ── STATS ────────────────────────────────────────────── --}}
    <div class="row g-4 mb-4">

        {{-- Ressources --}}
        <div class="col-md-6 col-xl-3">
            <div class="soft-card stats-card">
                <div class="stats-icon icon-green">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="stats-label">Ressources</div>
                <div class="stats-value">{{ $stats['total_ressources'] }}</div>
                <div class="stats-bottom">
                    <span class="text-success">● {{ $stats['disponibles'] }} disponibles</span>
                    <span class="text-muted">● {{ $stats['occupees'] }} occupées</span>
                </div>
            </div>
        </div>

        {{-- Réservations --}}
        <div class="col-md-6 col-xl-3">
            <div class="soft-card stats-card">
                <div class="stats-icon icon-orange">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div class="stats-label">Réservations</div>
                <div class="stats-value">{{ $stats['total_reservations'] }}</div>
                <div class="stats-bottom">
                    <span class="text-warning">● {{ $stats['en_attente'] }} attente</span>
                    <span class="text-success">● {{ $stats['valide_admin'] }} validées</span>
                </div>
            </div>
        </div>

        {{-- Utilisation --}}
        <div class="col-md-6 col-xl-3">
            <div class="soft-card stats-card">
                <div class="stats-icon icon-blue">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stats-label">Taux d'utilisation</div>
                <div class="stats-value">{{ $stats['taux_utilisation'] }}%</div>
                <div class="progress mt-4 rounded-pill"
                     style="height:10px;background:#dcefe8;">
                    <div class="progress-bar"
                         style="width:{{ $stats['taux_utilisation'] }}%;
                                background:linear-gradient(90deg,#15956d,#38c68f)">
                    </div>
                </div>
            </div>
        </div>

        {{-- Alertes --}}
        <div class="col-md-6 col-xl-3">
            <div class="soft-card stats-card">
                <div class="stats-icon icon-red">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="stats-label">Licences à renouveler</div>
                <div class="stats-value">{{ $stats['licences_alerte'] }}</div>
                <div class="stats-bottom">
                    <span class="text-danger">● alertes critiques</span>
                    <span class="text-muted">● {{ $stats['conflits'] }} conflits </span>
                </div>
            </div>
        </div>

    </div>

    {{-- ── TABLE + CALENDRIER ───────────────────────────────── --}}
    <div class="row g-4">

        {{-- TABLE --}}
        <div class="col-xl-7">
            <div class="soft-card table-card h-100">

                <div class="card-head-modern">
                    <h5><i class="bi bi-clock-history me-2"></i>Réservations récentes</h5>
                    <a href="{{ route('reservations.index') }}" class="btn btn-modern">Voir tout</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Personnel</th>
                                <th>Division</th>
                                <th>Ressource</th>
                                <th>Début</th>
                                <th>Statut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservationsRecentes as $r)
                            <tr>
                                <td>{{ $r->personnel?->prenom }} {{ $r->personnel?->nom }}</td>
                                <td>
                                    <span class="badge-soft badge-blue">
                                        {{ $r->personnel?->division?->code ?? '—' }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($r->ressource?->nom, 25) }}</td>
                                <td>{{ $r->date_debut?->format('d/m H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $r->badge_color }}">
                                        {{ $r->statut_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('reservations.show', $r) }}"
                                       class="btn btn-light rounded-circle shadow-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    Aucune réservation récente
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        {{-- CALENDRIER --}}
        <div class="col-xl-5">
            <div class="soft-card calendar-card h-100">

                <div class="calendar-header">
                    <div class="calendar-title">
                        {{ now()->translatedFormat('F Y') }}
                    </div>
                </div>

                <div class="calendar-body p-2">
                    <div id="vue-calendar"
                         data-reservations="{{ json_encode($reservationsRecentes->map(fn($r) => [
                             'id'         => $r->id,
                             'ressource'  => $r->ressource?->nom ?? '',
                             'personnel'  => ($r->personnel?->prenom ?? '') . ' ' . ($r->personnel?->nom ?? ''),
                             'date_debut' => $r->date_debut?->format('Y-m-d H:i') ?? '',
                             'date_fin'   => $r->date_fin?->format('Y-m-d H:i') ?? '',
                             'statut'     => $r->statut,
                         ])) }}">
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- ── BAS : IA + LICENCES ──────────────────────────────── --}}
    <div class="row g-4 mt-1">

        {{-- MODULE IA --}}
        <div class="col-xl-7">
            <div class="ia-card h-100">

                <div class="ia-title">
                    <i class="bi bi-robot me-2"></i>AIDE A LA DECISION
                </div>

                <div class="row">
                    <div class="col-4">
                        <div class="small opacity-75">Suggestions proposées</div>
                        <div class="ia-number">{{ $statsIa['total'] }}</div>
                    </div>
                    <div class="col-4">
                        <div class="small opacity-75">Acceptées</div>
                        <div class="ia-number">{{ $statsIa['acceptees'] }}</div>
                    </div>
                    <div class="col-4">
                        <div class="small opacity-75">Taux d'acceptation</div>
                        <div class="ia-number">{{ $statsIa['taux_acceptation'] }}%</div>
                    </div>
                </div>

                <div class="progress mt-4" style="height:10px;background:rgba(255,255,255,.2)">
                    <div class="progress-bar"
                         style="width:{{ $statsIa['taux_acceptation'] }}%;background:#a8ffd7">
                    </div>
                </div>

                @can('voir statistiques')
                <a href="{{ route('statistiques.ia') }}"
                   class="btn btn-light mt-4 rounded-pill px-4">
                    <i class="bi bi-bar-chart me-2"></i>Statistiques 
                </a>
                @endcan

            </div>
        </div>

        {{-- LICENCES --}}
        <div class="col-xl-5">
            @if($licencesAlerte->count() > 0)
            <div class="soft-card licence-card h-100">

                <div class="card-head-modern">
                    <h5>
                        <i class="bi bi-exclamation-circle text-danger me-2"></i>
                        Licences expire bientôt
                    </h5>
                </div>

                @foreach($licencesAlerte as $lic)
                <div class="licence-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ $lic->ressource?->nom }}</div>
                        <small class="text-muted">{{ $lic->editeur }}</small>
                    </div>
                    <span class="text-danger fw-semibold small">
                        Expire {{ $lic->date_fin_licence?->diffForHumans() }}
                    </span>
                </div>
                @endforeach

            </div>
            @endif
        </div>

    </div>

@endif {{-- fin @if($estPersonnel) --}}

@endsection

@push('scripts')
    @vite('resources/js/vue/main.js')
@endpush