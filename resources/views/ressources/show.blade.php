@extends('layouts.app')
@section('title','Détail Ressource')
@section('content')

<style>
  body{
    background-color: #E3F3FC;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <a href="{{ route('ressources.index') }}" class="btn btn-sm btn-outline-secondary me-2">
      <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="d-inline fw-bold" style="color:#1F3864">{{ $ressource->nom }}</h4>
    <span class="badge bg-{{ $ressource->badge_color }} ms-2 fs-6">
      {{ ucfirst(str_replace('_',' ',$ressource->etat)) }}
    </span>
  </div>

  <div class="d-flex gap-2">
    @can('creer reservations')
      @if($ressource->etat==='disponible')
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalReserver"
                data-id="{{ $ressource->id }}" data-nom="{{ $ressource->nom }}">
          <i class="bi bi-calendar-plus me-1"></i>Réserver
        </button>
      @endif
    @endcan

    @can('gerer ressources')
      <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEdit">
        <i class="bi bi-pencil me-1"></i>Modifier
      </button>
    @endcan
  </div>
</div>

<div class="row g-3">
  <!-- Colonne gauche -->
  <div class="col-lg-4">

    <!-- Image -->
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-body text-center p-3">
        @if($ressource->image)
          <img src="{{ Storage::url($ressource->image) }}" class="img-fluid rounded" style="max-height:200px">
        @else
          <div class="rounded d-flex align-items-center justify-content-center"
               style="height:180px;background:#f0f4f8">
            <i class="bi {{ $ressource->type_ressource==='materiel' ? 'bi-cpu' : 'bi-box' }} text-muted"
               style="font-size:60px"></i>
          </div>
        @endif
      </div>
    </div>

    <!-- Infos -->
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-header bg-white fw-semibold">
        <i class="bi bi-info-circle me-2"></i>Informations
      </div>
      <div class="card-body">
        <dl class="row mb-0 small">
          <dt class="col-5 text-muted">Type</dt>
          <dd class="col-7">
            <span class="badge {{ $ressource->type_ressource==='materiel'?'bg-primary':'bg-info' }}">
              {{ ucfirst($ressource->type_ressource) }}
            </span>
          </dd>

          <dt class="col-5 text-muted">Statut</dt>
          <dd class="col-7">
            <span class="badge bg-{{ $ressource->badge_color }}">
              {{ ucfirst(str_replace('_',' ',$ressource->etat)) }}
            </span>
          </dd>

          <dt class="col-5 text-muted">Mode</dt>
          <dd class="col-7">{{ ucfirst($ressource->mode_partage) }}</dd>

          <dt class="col-5 text-muted">Division</dt>
          <dd class="col-7">{{ $ressource->division?->code ?? '—' }}</dd>

          <dt class="col-5 text-muted">Localisation</dt>
          <dd class="col-7">{{ $ressource->localisation ?? '—' }}</dd>

          <dt class="col-5 text-muted">N° Série</dt>
          <dd class="col-7"><code>{{ $ressource->numero_serie ?? '—' }}</code></dd>
        </dl>
      </div>
    </div>

    <!-- Tags -->
    @if($ressource->tags->count()>0)
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-header bg-white fw-semibold">
        <i class="bi bi-tags me-2"></i>Tags
      </div>
      <div class="card-body">
        @foreach($ressource->tags as $tag)
          <span class="badge bg-light text-dark border me-1 mb-1">{{ $tag->libelle }}</span>
        @endforeach
      </div>
    </div>
    @endif
  </div>

  <!-- Colonne droite -->
  <div class="col-lg-8">

    <!-- Fiche matérielle -->
    @if($ressource->type_ressource==='materiel' && $ressource->ficheMaterielle)
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-header bg-white fw-semibold">
        <i class="bi bi-cpu me-2"></i>Fiche Matérielle
      </div>
      <div class="card-body">
        <div class="row g-2 small">
          <div class="col-md-3 text-muted">Marque / Modèle</div>
          <div class="col-md-9">
            <strong>{{ $ressource->ficheMaterielle->marque }} {{ $ressource->ficheMaterielle->modele }}</strong>
          </div>

          <div class="col-md-3 text-muted">Date acquisition</div>
          <div class="col-md-9">
            {{ $ressource->ficheMaterielle->date_acquisition?->format('d/m/Y') ?? '—' }}
          </div>

          <div class="col-md-3 text-muted">Capacité</div>
          <div class="col-md-9">{{ $ressource->ficheMaterielle->capacite ?? '—' }}</div>

          <div class="col-md-3 text-muted">Valeur achat</div>
          <div class="col-md-9">{{ $ressource->ficheMaterielle->valeur_formattee }}</div>
        </div>
      </div>
    </div>
    @endif

    <!-- Fiche logicielle -->
    @if($ressource->type_ressource==='logiciel' && $ressource->ficheLogicielle)
    @php $fiche = $ressource->ficheLogicielle; @endphp
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-header bg-white fw-semibold">
        <i class="bi bi-key me-2"></i>Fiche Logicielle
      </div>
      <div class="card-body">
        <div class="row g-2 small">
          <div class="col-md-3 text-muted">Éditeur / Version</div>
          <div class="col-md-9">
            <strong>{{ $fiche->editeur }} v{{ $fiche->version }}</strong>
          </div>

          <div class="col-md-3 text-muted">Type licence</div>
          <div class="col-md-9">{{ ucfirst($fiche->type_licence) }}</div>

          <div class="col-md-3 text-muted">Licences</div>
          <div class="col-md-9">
            <div class="d-flex align-items-center gap-2">
              <span class="fw-bold">{{ $fiche->cles_utilisees }} / {{ $fiche->nombre_licence }}</span>
              utilisées
              <div class="progress flex-grow-1" style="height:8px">
                <div class="progress-bar {{ $fiche->taux_utilisation>80?'bg-danger':'bg-success' }}"
                     style="width:{{ $fiche->taux_utilisation }}%"></div>
              </div>
              <span class="text-muted">{{ $fiche->taux_utilisation }}%</span>
            </div>
          </div>

          <div class="col-md-3 text-muted">Validité</div>
          <div class="col-md-9">
            {{ $fiche->date_debut_licence?->format('d/m/Y') ?? '—' }}
            @if($fiche->date_fin_licence)
              → <span class="{{ $fiche->bientot_expire ? 'text-danger fw-bold' : '' }}">
                {{ $fiche->date_fin_licence->format('d/m/Y') }}
              </span>
              @if($fiche->bientot_expire)
                <span class="badge bg-danger ms-1">Expire bientôt !</span>
              @endif
            @endif
          </div>
        </div>
      </div>
    </div>
    @endif

  </div>
</div>

@endsection