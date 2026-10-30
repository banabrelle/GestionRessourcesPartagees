@extends('layouts.app')

@section('title', 'Paramètres - GERES CENADI')

@section('content')

<style>
  body{
    background-color: #E3F3FC;
  }
</style>
<div class="container-fluid py-3">

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">
                <i class="bi bi-gear-fill text-primary me-2"></i>Paramètres
            </h2>
            <small class="text-muted">
                Gestion des divisions, services, bureaux et utilisateurs
            </small>
        </div>
    </div>

    <!-- Onglets Bootstrap -->
    <ul class="nav nav-tabs mb-3" id="parametresTabs" role="tablist">

        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'divisions' ? 'active' : '' }}"
                    id="divisions-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#divisions"
                    type="button"
                    role="tab"
                    aria-controls="divisions"
                    aria-selected="{{ $activeTab === 'divisions' ? 'true' : 'false' }}">
                <i class="bi bi-diagram-3 me-1"></i>Divisions
                <span class="badge bg-secondary ms-1" style="font-size:10px">
                    {{ \App\Models\Division::count() }}
                </span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'services' ? 'active' : '' }}"
                    id="services-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#services"
                    type="button"
                    role="tab"
                    aria-controls="services"
                    aria-selected="{{ $activeTab === 'services' ? 'true' : 'false' }}">
                <i class="bi bi-diagram-2 me-1"></i>Services
                <span class="badge bg-secondary ms-1" style="font-size:10px">
                    {{ \App\Models\Service::count() }}
                </span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'bureaux' ? 'active' : '' }}"
                    id="bureaux-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#bureaux"
                    type="button"
                    role="tab"
                    aria-controls="bureaux"
                    aria-selected="{{ $activeTab === 'bureaux' ? 'true' : 'false' }}">
                <i class="bi bi-door-open me-1"></i>Bureaux
                <span class="badge bg-secondary ms-1" style="font-size:10px">
                    {{ \App\Models\Bureau::count() }}
                </span>
            </button>
        </li>

        @can('voir personnel')
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'utilisateurs' ? 'active' : '' }}"
                    id="utilisateurs-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#utilisateurs"
                    type="button"
                    role="tab"
                    aria-controls="utilisateurs"
                    aria-selected="{{ $activeTab === 'utilisateurs' ? 'true' : 'false' }}">
                <i class="bi bi-people me-1"></i>Utilisateurs
                <span class="badge bg-secondary ms-1" style="font-size:10px">
                    {{ \App\Models\Personnel::count() }}
                </span>
            </button>
        </li>
        @endcan

    </ul>

    <!-- Contenu des onglets -->
    <div class="tab-content" id="parametresTabsContent">

        <!-- ONGLET DIVISIONS -->
        <div class="tab-pane fade {{ $activeTab === 'divisions' ? 'show active' : '' }}"
             id="divisions"
             role="tabpanel"
             aria-labelledby="divisions-tab">
            @include('parametres.divisions.index')
        </div>

        <!-- ONGLET SERVICES -->
        <div class="tab-pane fade {{ $activeTab === 'services' ? 'show active' : '' }}"
             id="services"
             role="tabpanel"
             aria-labelledby="services-tab">
            @include('parametres.services.index')
        </div>

        <!-- ONGLET BUREAUX -->
        <div class="tab-pane fade {{ $activeTab === 'bureaux' ? 'show active' : '' }}"
             id="bureaux"
             role="tabpanel"
             aria-labelledby="bureaux-tab">
            @include('parametres.bureaux.index')
        </div>

        @can('voir personnel')
        <!-- ONGLET UTILISATEURS -->
        <div class="tab-pane fade {{ $activeTab === 'utilisateurs' ? 'show active' : '' }}"
             id="utilisateurs"
             role="tabpanel"
             aria-labelledby="utilisateurs-tab">
            @include('personnels._table')
        </div>
        @endcan

    </div>
</div>
@endsection