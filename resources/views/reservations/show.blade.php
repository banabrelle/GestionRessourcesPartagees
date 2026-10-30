@extends('layouts.app')
@section('title','Détail Réservation')
@section('content')

<style>
  body{
    background-color: #E3F3FC;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <a href="{{ route('reservations.index') }}" class="btn btn-sm btn-outline-secondary me-2">
      <i class="bi bi-arrow-left"></i>
    </a>

    <h4 class="d-inline fw-bold" style="color:#1F3864">
      Réservation #{{ $reservation->id }}
    </h4>

    <span class="badge bg-{{ $reservation->badge_color }} ms-2 fs-6">
      {{ $reservation->statut_label }}
    </span>

    @if($reservation->conflit_detecte)
      <span class="badge bg-warning text-dark ms-1">
        <i class="bi bi-robot me-1"></i>Conflit détecté
      </span>
    @endif
  </div>

  @if($reservation->bonEmprunt)
    <a href="{{ route('bons.telecharger',$reservation->bonEmprunt) }}" class="btn btn-dark btn-sm">
      <i class="bi bi-file-pdf me-1"></i>
      Télécharger bon N° {{ $reservation->bonEmprunt->numero_bon }}
    </a>
  @endif
</div>

<div class="row g-3">

  <!-- Colonne gauche -->
  <div class="col-md-6">
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-header bg-white fw-semibold">
        <i class="bi bi-file-text me-2"></i>Détails de la demande
      </div>

      <div class="card-body">
        <dl class="row mb-0 small">

          <dt class="col-4 text-muted">Demandeur</dt>
          <dd class="col-8">
            <strong>{{ $reservation->personnel->prenom }} {{ $reservation->personnel->nom }}</strong><br>
            <small class="text-muted">
              {{ $reservation->personnel->matricule }} — {{ $reservation->personnel->division?->code }}
            </small>
          </dd>

          <dt class="col-4 text-muted">Ressource</dt>
          <dd class="col-8">
            <a href="{{ route('ressources.show',$reservation->ressource) }}">
              {{ $reservation->ressource->nom }}
            </a>
          </dd>

          <dt class="col-4 text-muted">Début</dt>
          <dd class="col-8">
            {{ $reservation->date_debut->isoFormat('dddd D MMMM YYYY [à] H[h]mm') }}
          </dd>

          <dt class="col-4 text-muted">Fin</dt>
          <dd class="col-8">
            {{ $reservation->date_fin->isoFormat('dddd D MMMM YYYY [à] H[h]mm') }}
          </dd>

          <dt class="col-4 text-muted">Durée</dt>
          <dd class="col-8">{{ $reservation->duree_heures }} heure(s)</dd>

          <dt class="col-4 text-muted">Motif</dt>
          <dd class="col-8">{{ $reservation->motif }}</dd>

          <dt class="col-4 text-muted">Soumise le</dt>
          <dd class="col-8">{{ $reservation->created_at->format('d/m/Y H:i') }}</dd>

        </dl>
      </div>
    </div>
  </div>

  <!-- Colonne droite -->
  <div class="col-md-6">

    <!-- Suivi -->
    <div class="card shadow-sm border-0 mb-3">
      <div class="card-header bg-white fw-semibold">
        <i class="bi bi-check2-all me-2"></i>Suivi de validation
      </div>

      <div class="card-body">

        <!-- Étape 1 -->
        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-3"
               style="width:36px;height:36px">
            <i class="bi bi-check text-white"></i>
          </div>
          <div>
            <div class="fw-semibold">Demande soumise</div>
            <small class="text-muted">
              {{ $reservation->created_at->format('d/m/Y H:i') }}
            </small>
          </div>
        </div>

        <!-- Étape 2 -->
        @php
          $chefOk = in_array($reservation->statut,['valide_chef','valide_admin','termine']);
        @endphp

        <div class="d-flex align-items-center mb-3">
          <div class="rounded-circle {{ $chefOk?'bg-success':'bg-light border' }} d-flex align-items-center justify-content-center me-3"
               style="width:36px;height:36px">
            <i class="bi {{ $chefOk?'bi-check text-white':'bi-clock text-muted' }}"></i>
          </div>

          <div>
            <div class="fw-semibold">Validation Chef de Division</div>

            @if($reservation->validePar)
              <small class="text-muted">
                {{ $reservation->validePar->prenom }} {{ $reservation->validePar->nom }}
                — {{ $reservation->date_val_chef?->format('d/m/Y H:i') }}
              </small>

              @if($reservation->commentaire_chef)
                <br>
                <small class="fst-italic">"{{ $reservation->commentaire_chef }}"</small>
              @endif
            @else
              <small class="text-muted">En attente...</small>
            @endif
          </div>
        </div>

        <!-- Étape 3 -->
        @php
          $adminOk = in_array($reservation->statut,['valide_admin','termine']);
        @endphp

        <div class="d-flex align-items-center">
          <div class="rounded-circle {{ $adminOk?'bg-success':'bg-light border' }} d-flex align-items-center justify-content-center me-3"
               style="width:36px;height:36px">
            <i class="bi {{ $adminOk?'bi-check text-white':'bi-clock text-muted' }}"></i>
          </div>

          <div>
            <div class="fw-semibold">Validation Administrative</div>

            @if($reservation->valideParAdmin)
              <small class="text-muted">
                {{ $reservation->valideParAdmin->prenom }} {{ $reservation->valideParAdmin->nom }}
                — {{ $reservation->date_val_admin?->format('d/m/Y H:i') }}
              </small>

              @if($reservation->commentaire_admin)
                <br>
                <small class="fst-italic">"{{ $reservation->commentaire_admin }}"</small>
              @endif
            @else
              <small class="text-muted">En attente...</small>
            @endif
          </div>
        </div>

      </div>
    </div>

    <!-- Actions -->
    {{-- <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">
        <i class="bi bi-lightning me-2"></i>Actions
      </div>

      <div class="card-body d-flex flex-wrap gap-2">

        @if($reservation->statut==='en_attente')

          @can('valider chef reservations')
            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalValiderChef">
              <i class="bi bi-check me-1"></i>Valider (Chef)
            </button>

            <button class="btn btn-outline-danger btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalRejeter"
                    data-type="chef">
              <i class="bi bi-x me-1"></i>Rejeter
            </button>
          @endcan

        @endif

      </div>
    </div>

  </div>
</div> --}}

<td>
  <div class="btn-group btn-group-sm">

    {{-- Voir le détail --}}
    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-outline-info">
      <i class="bi bi-eye"></i>
    </a>

    {{-- VALIDATION CHEF --}}
    @if($reservation->statut === 'en_attente')

      @if(auth()->user()->hasRole('chef_division') || auth()->user()->hasRole('administrateur'))

        {{-- Valider chef --}}
        <button class="btn btn-info text-white"
                data-bs-toggle="modal"
                data-bs-target="#modal-valider-chef-{{ $reservation->id }}">
          <i class="bi bi-check-circle"></i>
        </button>

        {{-- Rejeter chef --}}
        <button class="btn btn-outline-danger"
                data-bs-toggle="modal"
                data-bs-target="#modal-rejeter-chef-{{ $reservation->id }}">
          <i class="bi bi-x-circle"></i>
        </button>

      @endif

      {{-- Annuler (demandeur uniquement) --}}
      @if(auth()->user()->personnel?->id === $reservation->personnel_id)
        <form method="POST"
              action="{{ route('reservations.annuler', $reservation) }}"
              class="d-inline"
              onsubmit="return confirm('Annuler cette demande ?')">
          @csrf
          @method('PATCH')

          <button class="btn btn-outline-secondary">
            <i class="bi bi-x"></i>
          </button>
        </form>
      @endif

    @endif

    {{-- VALIDATION ADMIN --}}
    @if($reservation->statut === 'valide_chef' && auth()->user()->hasRole('administrateur'))

      <button class="btn btn-success"
              data-bs-toggle="modal"
              data-bs-target="#modal-valider-admin-{{ $reservation->id }}">
        <i class="bi bi-check2-all"></i>
      </button>

      <button class="btn btn-outline-danger"
              data-bs-toggle="modal"
              data-bs-target="#modal-rejeter-admin-{{ $reservation->id }}">
        <i class="bi bi-x-circle"></i>
      </button>

    @endif

    {{-- Télécharger bon --}}
    @if($reservation->bonEmprunt)
      <a href="{{ route('bons.telecharger', $reservation->bonEmprunt) }}"
         class="btn btn-outline-dark">
        <i class="bi bi-file-pdf"></i>
      </a>
    @endif

  </div>


  {{-- ================= MODALS ================= --}}

  {{-- ===== CHEF ===== --}}
  @if($reservation->statut === 'en_attente' &&
      (auth()->user()->hasRole('chef_division') || auth()->user()->hasRole('administrateur')))

  {{-- Valider chef --}}
  <div class="modal fade" id="modal-valider-chef-{{ $reservation->id }}" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">

        <form method="POST" action="{{ route('reservations.valider-chef', $reservation) }}">
          @csrf
          @method('PATCH')

          <div class="modal-header bg-info text-white">
            <h6 class="modal-title">
              Valider — {{ Str::limit($reservation->ressource->nom, 30) }}
            </h6>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="alert alert-light small">
              <strong>Demandeur :</strong> {{ $reservation->personnel->prenom }} {{ $reservation->personnel->nom }}<br>
              <strong>Période :</strong> {{ $reservation->date_debut->format('d/m/Y H:i') }}
              → {{ $reservation->date_fin->format('d/m/Y H:i') }}<br>
              <strong>Motif :</strong> {{ Str::limit($reservation->motif, 80) }}
            </div>

            <textarea name="commentaire"
                      class="form-control"
                      rows="2"
                      placeholder="Commentaire (optionnel)"></textarea>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-info text-white">Confirmer</button>
          </div>

        </form>

      </div>
    </div>
  </div>

  {{-- Rejeter chef --}}
  <div class="modal fade" id="modal-rejeter-chef-{{ $reservation->id }}" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">

        <form method="POST" action="{{ route('reservations.rejeter-chef', $reservation) }}">
          @csrf
          @method('PATCH')

          <div class="modal-header bg-danger text-white">
            <h6 class="modal-title">Rejeter la demande</h6>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <textarea name="commentaire"
                      class="form-control"
                      rows="3"
                      required
                      placeholder="Motif du rejet..."></textarea>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-danger">Rejeter</button>
          </div>

        </form>

      </div>
    </div>
  </div>

  @endif


  {{-- ===== ADMIN ===== --}}
  @if($reservation->statut === 'valide_chef' && auth()->user()->hasRole('administrateur'))

  {{-- Valider admin --}}
  <div class="modal fade" id="modal-valider-admin-{{ $reservation->id }}" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">

        <form method="POST" action="{{ route('reservations.valider-admin', $reservation) }}">
          @csrf
          @method('PATCH')

          <div class="modal-header bg-success text-white">
            <h6 class="modal-title">Validation finale</h6>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="alert alert-success small">
              Un bon PDF sera généré automatiquement.
            </div>

            <textarea name="commentaire"
                      class="form-control"
                      rows="2"
                      placeholder="Commentaire (optionnel)"></textarea>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-success">Valider</button>
          </div>

        </form>

      </div>
    </div>
  </div>

  {{-- Rejeter admin --}}
  <div class="modal fade" id="modal-rejeter-admin-{{ $reservation->id }}" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">

        <form method="POST" action="{{ route('reservations.rejeter-admin', $reservation) }}">
          @csrf
          @method('PATCH')

          <div class="modal-header bg-danger text-white">
            <h6 class="modal-title">Rejeter (Admin)</h6>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <textarea name="commentaire"
                      class="form-control"
                      rows="3"
                      required
                      placeholder="Motif obligatoire..."></textarea>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-danger">Rejeter</button>
          </div>

        </form>

      </div>
    </div>
  </div>

  @endif

</td>

@push('scripts')
<script>
document.getElementById('modalRejeter')?.addEventListener('show.bs.modal', e => {
  const t = e.relatedTarget?.dataset.type;
  document.getElementById('formRejeter').action =
    '/reservations/{{ $reservation->id }}/' + (t === 'chef' ? 'rejeter-chef' : 'rejeter-admin');
});
</script>
@endpush

@endsection