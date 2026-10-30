@extends('layouts.app')
@section('title','Ressources')

@section('content')

<style>
  body {
    background-color: #E3F3FC;
  }

  .table-scroll {
    max-height: 55vh;
    overflow-y: auto;
    overflow-x: auto;
  }

  .table-scroll thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background-color: #f8f9fa;
    box-shadow: 0 2px 4px rgba(0,0,0,.08);
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold mb-0" style="color:#1F3864">
    <i class="bi bi-box-seam me-2"></i>Ressources
  </h4>
  @can('gerer ressources')
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
    <i class="bi bi-plus-lg me-1"></i>Ajouter une ressource
  </button>
  @endcan
</div>

{{-- Onglets filtres statut --}}
<div class="card shadow-sm border-0 mb-3">
  <div class="card-body py-2">
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <span class="fw-semibold small me-1">Filtrer :</span>
      @foreach([
        ''                      => ['label'=>'Tous',       'icon'=>'bi-grid',         'color'=>'dark'],
        'disponible'            => ['label'=>'Disponible', 'icon'=>'bi-check-circle', 'color'=>'success'],
        'occupe'                => ['label'=>'Occupé',     'icon'=>'bi-x-circle',     'color'=>'danger'],
        'en_attente_validation' => ['label'=>'En attente', 'icon'=>'bi-clock',        'color'=>'warning'],
        'en_maintenance'        => ['label'=>'Maintenance','icon'=>'bi-wrench',       'color'=>'secondary'],
      ] as $etat => $cfg)
      @php
        $active = request('etat') === $etat;
        $cnt    = $etat === '' ? $compteurs['tous']
                : ($compteurs[$etat === 'en_attente_validation' ? 'en_attente' : $etat] ?? 0);
      @endphp
      <a href="{{ route('ressources.index', array_merge(request()->except('etat','page'), $etat ? ['etat'=>$etat] : [])) }}"
         class="btn btn-sm {{ $active ? 'btn-'.$cfg['color'] : 'btn-outline-'.$cfg['color'] }}">
        <i class="{{ $cfg['icon'] }} me-1"></i>{{ $cfg['label'] }}
        <span class="badge {{ $active ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ $cnt }}</span>
      </a>
      @endforeach
    </div>
  </div>
</div>

{{-- Filtres avancés --}}
<div class="card shadow-sm border-0 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      @if(request('etat'))<input type="hidden" name="etat" value="{{ request('etat') }}">@endif
      <div class="col-md-4">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="🔍 Nom, N° série..." value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <select name="type_ressource" class="form-select form-select-sm">
          <option value="">Tous types</option>
          <option value="materiel" {{ request('type_ressource')==='materiel'?'selected':'' }}>Matériel</option>
          <option value="logiciel" {{ request('type_ressource')==='logiciel'?'selected':'' }}>Logiciel</option>
        </select>
      </div>
      <div class="col-md-3">
        <select name="division_id" class="form-select form-select-sm">
          <option value="">Toutes divisions</option>
          @foreach($divisions as $d)
          <option value="{{ $d->id }}" {{ request('division_id')==$d->id?'selected':'' }}>
            {{ $d->code }} — {{ Str::limit($d->nom,30) }}
          </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-sm btn-primary w-100">
          <i class="bi bi-search"></i> Filtrer
        </button>
      </div>
      <div class="col-md-1">
        <a href="{{ route('ressources.index') }}" class="btn btn-sm btn-outline-secondary w-100">
          <i class="bi bi-x-lg"></i>
        </a>
      </div>
    </form>
  </div>
</div>

{{-- Liste --}}
<div class="card shadow-sm border-0">
  <div class="card-body p-0">
    <div class="table-scroll">
      <table class="table table-hover table-striped mb-0">
        <thead>
          <tr>
            <th>Nom</th><th>Type</th><th>Division</th>
            <th>Statut</th><th>Tags</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($ressources as $res)
          <tr>
            <td>
              <a href="{{ route('ressources.show',$res) }}" class="fw-semibold text-decoration-none">
                {{ $res->nom }}
              </a>
              @if($res->type_ressource==='materiel' && $res->ficheMaterielle)
                <br><small class="text-muted">{{ $res->ficheMaterielle->marque }} {{ $res->ficheMaterielle->modele }}</small>
              @elseif($res->type_ressource==='logiciel' && $res->ficheLogicielle)
                <br><small class="text-muted">{{ $res->ficheLogicielle->editeur }} v{{ $res->ficheLogicielle->version }}</small>
              @endif
            </td>
            <td>
              <span class="badge {{ $res->type_ressource==='materiel' ? 'bg-primary' : 'bg-info' }}">
                {{ ucfirst($res->type_ressource) }}
              </span>
            </td>
            <td><span class="badge bg-secondary">{{ $res->division?->code ?? '—' }}</span></td>
            <td>
              <span class="badge badge-{{ str_replace('_','-',$res->etat) }}">
                {{ ucfirst(str_replace('_',' ',$res->etat)) }}
              </span>
            </td>
            <td>
              @foreach($res->tags->take(3) as $tag)
              <span class="badge bg-light text-dark border" style="font-size:10px">{{ $tag->libelle }}</span>
              @endforeach
            </td>
            <td>
              <div class="btn-group btn-group-sm">
                <a href="{{ route('ressources.show',$res) }}" class="btn btn-outline-info" title="Voir">
                  <i class="bi bi-eye"></i>
                </a>
                @can('creer reservations')
                @if($res->etat==='disponible')
                <button class="btn btn-success btn-reserver" title="Réserver"
                        data-bs-toggle="modal" data-bs-target="#modalReserver"
                        data-id="{{ $res->id }}"
                        data-nom="{{ $res->nom }}">
                  <i class="bi bi-calendar-plus"></i>
                </button>
                @endif
                @endcan
                @can('gerer ressources')
                <button class="btn btn-outline-warning btn-edit" title="Modifier"
                        data-bs-toggle="modal" data-bs-target="#modalEdit"
                        data-res='{{ json_encode([
                            "id"           => $res->id,
                            "nom"          => $res->nom,
                            "etat"         => $res->etat,
                            "type"         => $res->type_ressource,
                            "mode_partage" => $res->mode_partage,
                            "division_id"  => $res->division_id,
                            "numero_serie" => $res->numero_serie,
                            "description"  => $res->description,
                        ]) }}'>
                  <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="{{ route('ressources.destroy',$res) }}"
                      class="d-inline"
                      onsubmit="return confirm('Supprimer cette ressource ?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-outline-danger" title="Supprimer">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
                @endcan
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-5 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2"></i>Aucune ressource trouvée
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $ressources->links() }}</div>
  </div>
</div>

{{-- ══ MODAL CRÉER ══ --}}
@can('gerer ressources')
<div class="modal fade" id="modalCreate" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form method="POST" action="{{ route('ressources.store') }}">
        @csrf
        <div class="modal-header" style="background:#1F3864">
          <h5 class="modal-title text-white"><i class="bi bi-plus-circle me-2"></i>Ajouter une ressource</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
              <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
              <select name="type_ressource" class="form-select" required id="selectType"
                      onchange="toggleTypeFields(this.value)">
                <option value="materiel">Matériel</option>
                <option value="logiciel">Logiciel</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Mode partage</label>
              <select name="mode_partage" class="form-select">
                <option value="exclusif">Exclusif</option>
                <option value="partage">Partagé</option>
                <option value="reserve">Réservé</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Division</label>
              <select name="division_id" class="form-select">
                <option value="">— Sélectionner —</option>
                @foreach($divisions as $d)
                <option value="{{ $d->id }}">{{ $d->code }} — {{ $d->nom }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">N° Série</label>
              <input type="text" name="numero_serie" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Tags</label>
              <select name="tags[]" class="form-select" multiple>
                @foreach($tags as $t)
                <option value="{{ $t->id }}">{{ $t->libelle }}</option>
                @endforeach
              </select>
              <small class="text-muted">Ctrl+clic pour sélection multiple</small>
            </div>
            <div id="champseMateriel" class="col-12">
              <div class="row g-3">
                <div class="col-md-3">
                  <label class="form-label">Marque</label>
                  <input type="text" name="marque" class="form-control">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Modèle</label>
                  <input type="text" name="modele" class="form-control">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Date acquisition</label>
                  <input type="date" name="date_acquisition" class="form-control">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Valeur achat (FCFA)</label>
                  <input type="number" name="valeur_achat" class="form-control">
                </div>
              </div>
            </div>
            <div id="champsLogiciel" class="col-12" style="display:none">
              <div class="row g-3">
                <div class="col-md-3">
                  <label class="form-label">Éditeur</label>
                  <input type="text" name="editeur" class="form-control">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Version</label>
                  <input type="text" name="version" class="form-control">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Nb licences</label>
                  <input type="number" name="nombre_licence" class="form-control" min="1" value="1">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Type licence</label>
                  <select name="type_licence" class="form-select">
                    <option value="proprietaire">Propriétaire</option>
                    <option value="libre">Libre</option>
                    <option value="abonnement">Abonnement</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Expire le</label>
                  <input type="date" name="date_fin_licence" class="form-control">
                </div>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>Enregistrer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══ MODAL MODIFIER ══ --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" id="formEdit">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <div class="modal-header" style="background:#fd7e14">
          <h5 class="modal-title text-white"><i class="bi bi-pencil me-2"></i>Modifier la ressource</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
              <input type="text" name="nom" id="editNom" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Statut</label>
              <select name="etat" id="editEtat" class="form-select">
                <option value="disponible">Disponible</option>
                <option value="occupe">Occupé</option>
                <option value="en_attente_validation">En attente</option>
                <option value="en_maintenance">En maintenance</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Mode partage</label>
              <select name="mode_partage" id="editMode" class="form-select">
                <option value="exclusif">Exclusif</option>
                <option value="partage">Partagé</option>
                <option value="reserve">Réservé</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Division</label>
              <select name="division_id" id="editDivision" class="form-select">
                <option value="">— Aucune —</option>
                @foreach($divisions as $d)
                <option value="{{ $d->id }}">{{ $d->code }} — {{ $d->nom }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">N° Série</label>
              <input type="text" name="numero_serie" id="editNumeroSerie" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" id="editDescription" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-warning text-white">
            <i class="bi bi-save me-1"></i>Mettre à jour
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endcan

{{-- ══ MODAL RÉSERVATION avec vérification IA ══ --}}
@can('creer reservations')
<div class="modal fade" id="modalReserver" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formReservation" method="POST" action="{{ route('reservations.store') }}">
        @csrf
        <input type="hidden" name="ressource_id" id="reserverRessourceId">
        <div class="modal-header" style="background:#198754">
          <h5 class="modal-title text-white">
            <i class="bi bi-calendar-plus me-2"></i>Nouvelle réservation
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-success py-2 mb-3">
            <i class="bi bi-box-seam me-2"></i>
            Ressource : <strong id="reserverNom"></strong>
          </div>
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label fw-semibold">Date début <span class="text-danger">*</span></label>
              <input type="datetime-local" name="date_debut" id="reserverDebut"
                     class="form-control" required
                     min="{{ now()->format('Y-m-d\TH:i') }}">
            </div>
            <div class="col-6">
              <label class="form-label fw-semibold">Date fin <span class="text-danger">*</span></label>
              <input type="datetime-local" name="date_fin" id="reserverFin"
                     class="form-control" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Motif <span class="text-danger">*</span></label>
              <textarea name="motif" id="reserverMotif" class="form-control" rows="3" required
                        placeholder="Décrivez l'utilisation de cette ressource (10 caractères min.)...">
              </textarea>
            </div>
          </div>
          {{-- Zone résultat de la vérification --}}
          <div id="zoneVerif" class="mt-3 d-none">
            <div id="verifResultat"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" id="btnSoumettre" class="btn btn-success">
            <i class="bi bi-send me-1"></i>Soumettre la demande
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══ MODAL CONFLIT + SUGGESTIONS IA ══ --}}
<div class="modal fade" id="modalConflit" tabindex="-1"
     data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          Conflit détecté — Suggestions IA
        </h5>
      </div>
      <div class="modal-body p-4">
        <div class="alert alert-warning d-flex gap-3 mb-4">
          <i class="bi bi-robot fs-3 flex-shrink-0"></i>
          <div>
            <strong>Cette ressource est déjà réservée sur le créneau demandé.</strong><br>
            <small class="text-muted">
              Choisissez un créneau alternatif ci-dessous, utilisez une ressource similaire,
              ou soumettez quand même votre demande.
            </small>
          </div>
        </div>

        {{-- Créneaux alternatifs --}}
        <h6 class="fw-bold mb-3">
          <i class="bi bi-clock-fill text-info me-2"></i>Créneaux disponibles
        </h6>
        <div class="row g-2 mb-4" id="listeCreneaux">
          <div class="col-12 text-center py-3">
            <div class="spinner-border text-info spinner-border-sm me-2"></div>
            Chargement des créneaux...
          </div>
        </div>

        {{-- Ressources similaires --}}
        <div id="sectionRessources" class="d-none">
          <hr>
          <h6 class="fw-bold mb-3">
            <i class="bi bi-box-seam-fill text-primary me-2"></i>
            Ressources similaires disponibles
          </h6>
          <div class="row g-2" id="listeRessources"></div>
        </div>
      </div>
      <div class="modal-footer bg-light justify-content-between">
        <button type="button" id="btnSoumettreQuandMeme" class="btn btn-outline-secondary">
          <i class="bi bi-send me-1"></i>Soumettre quand même (avec conflit signalé)
        </button>
        <button type="button" class="btn btn-warning" data-bs-dismiss="modal"
                onclick="rouvrirModalReservation()">
          <i class="bi bi-arrow-left me-1"></i>Modifier ma demande
        </button>
      </div>
    </div>
  </div>
</div>
@endcan

@push('scripts')
<script>
// ════════════════════════════════════════════════════════════════════
// ROUTES AJAX IA — utiliser route() de Laravel pour éviter les erreurs
// ════════════════════════════════════════════════════════════════════
const URL_VERIFIER_CONFLIT     = '{{ route("ajax.ia.verifier-conflit") }}';
const URL_SUGGERER_CRENEAUX    = '{{ route("ajax.ia.suggerer-creneaux") }}';
const URL_RESSOURCES_SIMILAIRES= '{{ route("ajax.ia.ressources-similaires") }}';
const CSRF                     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ════════════════════════════════════════════════════════════════════
// ÉTAT — garder en mémoire ce qu'on était en train de réserver
// ════════════════════════════════════════════════════════════════════
let demandeEnCours = {
    ressource_id: null,
    debut: null,
    fin: null,
};

// ════════════════════════════════════════════════════════════════════
// ALIMENTER LA MODALE RÉSERVER quand on clique sur "Réserver"
// ════════════════════════════════════════════════════════════════════
document.querySelectorAll('.btn-reserver').forEach(function(btn) {
    btn.addEventListener('click', function() {
        demandeEnCours.ressource_id = this.dataset.id;
        document.getElementById('reserverRessourceId').value = this.dataset.id;
        document.getElementById('reserverNom').textContent   = this.dataset.nom;
        document.getElementById('reserverDebut').value       = '';
        document.getElementById('reserverFin').value         = '';
        document.getElementById('reserverMotif').value       = '';
        document.getElementById('zoneVerif').classList.add('d-none');
        document.getElementById('verifResultat').innerHTML   = '';

        // Réactiver le bouton (au cas où)
        const btn = document.getElementById('btnSoumettre');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send me-1"></i>Soumettre la demande';
        btn.onclick   = null;
    });
});

// ════════════════════════════════════════════════════════════════════
// INTERCEPTER LE SUBMIT → VÉRIFIER LE CONFLIT EN AJAX
// ════════════════════════════════════════════════════════════════════
document.getElementById('formReservation')?.addEventListener('submit', async function(event) {
    event.preventDefault();
    event.stopPropagation();

    const debut = document.getElementById('reserverDebut').value;
    const fin   = document.getElementById('reserverFin').value;
    const rid   = document.getElementById('reserverRessourceId').value;
    const motif = document.getElementById('reserverMotif').value;

    if (!debut || !fin || !rid || motif.length < 10) {
        afficherVerif('warning', '<i class="bi bi-exclamation-circle me-2"></i>Remplissez tous les champs (motif minimum 10 caractères).');
        return;
    }

    if (new Date(fin) <= new Date(debut)) {
        afficherVerif('warning', '<i class="bi bi-exclamation-circle me-2"></i>La date de fin doit être après la date de début.');
        return;
    }

    demandeEnCours.ressource_id = rid;
    demandeEnCours.debut        = debut;
    demandeEnCours.fin          = fin;

    const btnSoumettre = document.getElementById('btnSoumettre');
    btnSoumettre.disabled   = true;
    btnSoumettre.innerHTML  = '<span class="spinner-border spinner-border-sm me-1"></span>Vérification en cours...';

    try {
        const reponse = await appelAjax(URL_VERIFIER_CONFLIT, {
            ressource_id: rid,
            date_debut:   debut,
            date_fin:     fin,
        });

        if (reponse.conflit) {
            btnSoumettre.disabled  = false;
            btnSoumettre.innerHTML = '<i class="bi bi-send me-1"></i>Soumettre la demande';

            const modalReserver = bootstrap.Modal.getInstance(document.getElementById('modalReserver'));
            if (modalReserver) {
                document.getElementById('modalReserver').addEventListener('hidden.bs.modal', function handler() {
                    this.removeEventListener('hidden.bs.modal', handler);
                    ouvrirModaleConflit(rid, debut, fin);
                }, { once: true });
                modalReserver.hide();
            } else {
                ouvrirModaleConflit(rid, debut, fin);
            }

        } else {
            afficherVerif('success', '<i class="bi bi-check-circle-fill me-2"></i><strong>Créneau disponible !</strong> Soumission en cours...');
            setTimeout(() => this.submit(), 700);
        }

    } catch (erreur) {
        console.error('Erreur vérification IA:', erreur.message);

        btnSoumettre.disabled  = false;
        btnSoumettre.innerHTML = '<i class="bi bi-send me-1"></i>Soumettre la demande';

        afficherVerif('danger',
            `<div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                <div>
                    <strong>La vérification automatique a échoué :</strong>
                    <code class="d-block small mt-1">${erreur.message}</code>
                    <small class="text-muted">
                        Vous pouvez tout de même soumettre votre demande.
                        L'administrateur vérifiera la disponibilité.
                    </small>
                </div>
            </div>`
        );
    }
});

// ════════════════════════════════════════════════════════════════════
// OUVRIR LA MODALE CONFLIT ET CHARGER LES SUGGESTIONS
// ════════════════════════════════════════════════════════════════════
function ouvrirModaleConflit(rid, debut, fin) {
    document.getElementById('listeCreneaux').innerHTML =
        `<div class="col-12 text-center py-3">
            <div class="spinner-border text-info spinner-border-sm me-2"></div>
            Recherche des créneaux disponibles...
         </div>`;
    document.getElementById('sectionRessources').classList.add('d-none');
    document.getElementById('listeRessources').innerHTML = '';

    new bootstrap.Modal(document.getElementById('modalConflit')).show();

    const dureeH = Math.max(1, Math.round((new Date(fin) - new Date(debut)) / 3600000));

    appelAjax(URL_SUGGERER_CRENEAUX, { ressource_id: rid, duree_heures: dureeH, n: 3 })
        .then(function(data) {
            afficherCreneaux(data.creneaux || []);
        })
        .catch(function(err) {
            document.getElementById('listeCreneaux').innerHTML =
                `<div class="col-12">
                    <div class="alert alert-danger py-2 small">
                        <i class="bi bi-wifi-off me-2"></i>
                        Impossible de charger les créneaux : ${err.message}
                    </div>
                 </div>`;
        });

    appelAjax(URL_RESSOURCES_SIMILAIRES, { ressource_id: rid, date_debut: debut, date_fin: fin })
        .then(function(data) {
            if (data.ressources && data.ressources.length > 0) {
                afficherRessourcesSimilaires(data.ressources);
            }
        })
        .catch(function(err) {
            console.warn('Ressources similaires non disponibles:', err.message);
        });
}

// ════════════════════════════════════════════════════════════════════
// AFFICHER LES CRÉNEAUX SUGGÉRÉS
// ════════════════════════════════════════════════════════════════════
function afficherCreneaux(creneaux) {
    const container = document.getElementById('listeCreneaux');

    if (!creneaux.length) {
        container.innerHTML =
            `<div class="col-12">
                <div class="alert alert-info py-2">
                    <i class="bi bi-calendar-x me-2"></i>
                    Aucun créneau disponible trouvé dans les prochains jours ouvrables.
                </div>
             </div>`;
        return;
    }

    container.innerHTML = creneaux.map(function(c, i) {
        const score    = Math.round((c.score || 0) * 100);
        const label    = c.label || c.debut;
        const couleurs = ['info', 'primary', 'secondary'];
        const coul     = couleurs[i] || 'secondary';

        return `
            <div class="col-md-4">
                <div class="card border-2 border-${coul} h-100 creneau-card"
                     style="cursor:pointer; transition:.2s;"
                     onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,.15)';"
                     onmouseout="this.style.transform=''; this.style.boxShadow='';"
                     onclick="choisirCreneau('${c.debut}', '${c.fin}')">
                    <div class="card-body text-center p-3">
                        <div class="badge bg-${coul} ${coul === 'info' ? 'text-dark' : ''} mb-2">
                            Option ${i+1}
                        </div>
                        <div class="fw-bold">${label}</div>
                        <div class="text-muted small">Fin : ${c.fin}</div>
                        <div class="progress mt-2 mb-1" style="height:6px">
                            <div class="progress-bar bg-${coul}" style="width:${score}%"></div>
                        </div>
                        <small class="text-muted">Score : ${score}%</small>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-${coul} w-100 ${coul === 'info' ? 'text-dark' : ''}">
                                <i class="bi bi-check me-1"></i>Choisir ce créneau
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;
    }).join('');
}

// ════════════════════════════════════════════════════════════════════
// AFFICHER LES RESSOURCES SIMILAIRES
// ════════════════════════════════════════════════════════════════════
function afficherRessourcesSimilaires(ressources) {
    document.getElementById('sectionRessources').classList.remove('d-none');
    document.getElementById('listeRessources').innerHTML = ressources.map(function(r) {
        const score = Math.round((r.score || 0) * 100);
        return `
            <div class="col-md-4">
                <div class="card border-2 border-primary h-100"
                     style="cursor:pointer; transition:.2s;"
                     onmouseover="this.style.transform='translateY(-3px)';"
                     onmouseout="this.style.transform='';"
                     onclick="utiliserRessourceSimilaire(${r.id})">
                    <div class="card-body p-3">
                        <div class="badge bg-primary mb-2">Similarité ${score}%</div>
                        <div class="fw-bold">${r.nom}</div>
                        <small class="text-muted d-block">${r.division}</small>
                        ${r.marque ? `<small class="text-muted">${r.marque}</small>` : ''}
                        ${r.tags ? `<div class="mt-1"><span class="badge bg-light text-dark border" style="font-size:10px">${r.tags}</span></div>` : ''}
                        <div class="progress mt-2 mb-1" style="height:6px">
                            <div class="progress-bar bg-primary" style="width:${score}%"></div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary w-100 mt-1">
                            <i class="bi bi-arrow-right me-1"></i>Voir cette ressource
                        </button>
                    </div>
                </div>
            </div>`;
    }).join('');
}

// ════════════════════════════════════════════════════════════════════
// ACTIONS UTILISATEUR
// ════════════════════════════════════════════════════════════════════

function choisirCreneau(debut, fin) {
    const modalConflit = bootstrap.Modal.getInstance(document.getElementById('modalConflit'));
    if (modalConflit) {
        document.getElementById('modalConflit').addEventListener('hidden.bs.modal', function handler() {
            this.removeEventListener('hidden.bs.modal', handler);
            document.getElementById('reserverDebut').value = debut.replace(' ', 'T');
            document.getElementById('reserverFin').value   = fin.replace(' ', 'T');
            afficherVerif('success',
                `<i class="bi bi-check-circle-fill me-2"></i>
                 Créneau sélectionné : <strong>${debut}</strong> → <strong>${fin}</strong>.
                 Vérifiez le motif et soumettez.`);
            new bootstrap.Modal(document.getElementById('modalReserver')).show();
        }, { once: true });
        modalConflit.hide();
    }
}

function utiliserRessourceSimilaire(ressourceId) {
    const modalConflit = bootstrap.Modal.getInstance(document.getElementById('modalConflit'));
    if (modalConflit) modalConflit.hide();
    window.location.href = '/ressources/' + ressourceId;
}

function rouvrirModalReservation() {
    document.getElementById('modalConflit').addEventListener('hidden.bs.modal', function handler() {
        this.removeEventListener('hidden.bs.modal', handler);
        new bootstrap.Modal(document.getElementById('modalReserver')).show();
    }, { once: true });
}

document.getElementById('btnSoumettreQuandMeme')?.addEventListener('click', function() {
    const modalConflit = bootstrap.Modal.getInstance(document.getElementById('modalConflit'));
    if (modalConflit) {
        document.getElementById('modalConflit').addEventListener('hidden.bs.modal', function handler() {
            this.removeEventListener('hidden.bs.modal', handler);
            document.getElementById('formReservation').submit();
        }, { once: true });
        modalConflit.hide();
    } else {
        document.getElementById('formReservation').submit();
    }
});

// ════════════════════════════════════════════════════════════════════
// UTILITAIRES
// ════════════════════════════════════════════════════════════════════

async function appelAjax(url, payload) {
    const response = await fetch(url, {
        method:  'POST',
        headers: {
            'Content-Type':  'application/json',
            'Accept':        'application/json',
            'X-CSRF-TOKEN':  CSRF,
        },
        body: JSON.stringify(payload),
    });

    const contentType = response.headers.get('content-type') || '';
    if (!contentType.includes('application/json')) {
        if (response.status === 401 || response.status === 302) {
            throw new Error('Session expirée. Rechargez la page et reconnectez-vous.');
        }
        if (response.status === 404) {
            throw new Error(`Route introuvable : ${url}. Vérifiez routes/web.php.`);
        }
        throw new Error(`Réponse inattendue du serveur (HTTP ${response.status}).`);
    }

    const data = await response.json();

    if (!response.ok) {
        const message = data.message
            || (data.errors ? Object.values(data.errors).flat().join(', ') : null)
            || `Erreur HTTP ${response.status}`;
        throw new Error(message);
    }

    return data;
}

function afficherVerif(type, html) {
    document.getElementById('zoneVerif').classList.remove('d-none');
    document.getElementById('verifResultat').innerHTML =
        `<div class="alert alert-${type} py-2 small mb-0">${html}</div>`;
}

// Alimenter la modale d'édition
document.querySelectorAll('.btn-edit').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const d = JSON.parse(this.dataset.res);
        document.getElementById('formEdit').action         = '/ressources/' + d.id;
        document.getElementById('editNom').value           = d.nom          || '';
        document.getElementById('editEtat').value          = d.etat         || 'disponible';
        document.getElementById('editMode').value          = d.mode_partage || 'exclusif';
        document.getElementById('editDivision').value      = d.division_id  || '';
        document.getElementById('editNumeroSerie').value   = d.numero_serie || '';
        document.getElementById('editDescription').value   = d.description  || '';
    });
});

// Toggle champs matériel / logiciel dans la modale création
function toggleTypeFields(valeur) {
    document.getElementById('champseMateriel').style.display = valeur === 'materiel' ? '' : 'none';
    document.getElementById('champsLogiciel').style.display  = valeur === 'logiciel' ? '' : 'none';
}
</script>
@endpush

@endsection