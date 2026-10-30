@extends('layouts.app')
@section('title','Personnel')
 
@section('content')

<style>
  body {
    background-color: #E3F3FC;
  }

  /* Juste la zone de table qui défile — rien d'autre ne change */
  .table-scroll {
    max-height: 55vh;   /* ajustez ce % selon votre écran */
    overflow-y: auto;
    overflow-x: auto;
  }

  /* En-têtes figés pendant le défilement */
  .table-scroll thead th {
    position: sticky;
    top: 0;
    z-index: 2;
    background-color: #f8f9fa;
    box-shadow: 0 2px 4px rgba(0,0,0,.08);
  }
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0" style="color:#1F3864">
            <i class="bi bi-people me-2"></i>Gestion du Personnel
        </h4>
        <small class="text-muted">{{ $personnels->total() }} agent(s) au total</small>
    </div>
    @can('creer personnel')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
        <i class="bi bi-person-plus me-1"></i>Ajouter un agent
    </button>
    @endcan
</div>
 
{{-- ── Filtres ── --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="🔍 Nom, prénom, matricule..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="division_id" class="form-select form-select-sm">
                    <option value="">Toutes divisions</option>
                    @foreach($divisions as $d)
                    <option value="{{ $d->id }}" {{ request('division_id')==$d->id?'selected':'' }}>
                        {{ $d->code }} — {{ $d->nom }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select form-select-sm">
                    <option value="">Tous rôles</option>
                    <option value="personnel"      {{ request('role')==='personnel'?'selected':'' }}>Personnel</option>
                    <option value="chef_division"  {{ request('role')==='chef_division'?'selected':'' }}>Chef division</option>
                    <option value="administrateur" {{ request('role')==='administrateur'?'selected':'' }}>Administrateur</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i> Filtrer
                </button>
                <a href="{{ route('personnels.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>
 
{{-- ── Liste défilante ── --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th>Nom / Prénom</th>
                        <th>Matricule</th>
                        <th>Division</th>
                        <th>Service</th>
                        <th>Bureau</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($personnels as $per)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                {{-- Avatar initiales --}}
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                     style="width:34px;height:34px;font-size:12px;background:#1F3864">
                                    {{ strtoupper(substr($per->prenom,0,1).substr($per->nom,0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $per->prenom }} {{ $per->nom }}</div>
                                    <small class="text-muted">{{ $per->user?->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td><code class="small">{{ $per->matricule }}</code></td>
                        <td>
                            <span class="badge" style="background:#1F3864">
                                {{ $per->division?->code ?? '—' }}
                            </span>
                        </td>
                        <td><small>{{ Str::limit($per->service?->nom ?? '—', 22) }}</small></td>
                        <td><small>{{ $per->bureau?->nom ?? '—' }}</small></td>
                        <td>
                            @php
                                $role = $per->user?->getRoleNames()->first() ?? '—';
                                $roleColor = match($role) {
                                    'administrateur' => 'danger',
                                    'chef_division'  => 'warning text-dark',
                                    'personnel'      => 'info text-dark',
                                    default          => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $roleColor }}">{{ ucfirst(str_replace('_',' ',$role)) }}</span>
                        </td>
                        <td>
                            @if($per->statut)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-danger">Inactif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                {{-- Voir détail --}}
                                <a href="{{ route('personnels.show', $per) }}"
                                   class="btn btn-outline-info" title="Voir profil">
                                    <i class="bi bi-eye"></i>
                                </a>
 
                                @can('modifier personnel')
                                {{-- Modifier --}}
                                <button class="btn btn-outline-warning btn-edit-per"
                                        title="Modifier"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit"
                                        data-per='{{ json_encode([
                                            "id"          => $per->id,
                                            "nom"         => $per->nom,
                                            "prenom"      => $per->prenom,
                                            "telephone"   => $per->telephone,
                                            "division_id" => $per->division_id,
                                            "service_id"  => $per->service_id,
                                            "bureau_id"   => $per->bureau_id,
                                            "email"       => $per->email,
                                            "matricule"   => $per->matricule,
                                            "role"        => $per->user?->getRoleNames()->first(),
                                            "name"        => $per->user?->name,
                                        ]) }}'>
                                    <i class="bi bi-pencil"></i>
                                </button>
 
                                {{-- Activer / Désactiver --}}
                                <button class="btn {{ $per->statut ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                        title="{{ $per->statut ? 'Désactiver le compte' : 'Activer le compte' }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalToggle"
                                        data-id="{{ $per->id }}"
                                        data-nom="{{ $per->prenom }} {{ $per->nom }}"
                                        data-statut="{{ $per->statut ? '1' : '0' }}">
                                    <i class="bi bi-{{ $per->statut ? 'pause-circle' : 'play-circle' }}"></i>
                                </button>
                                @endcan
 
                                @can('supprimer personnel')
                                <form method="POST" action="{{ route('personnels.destroy', $per) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Supprimer définitivement {{ $per->prenom }} {{ $per->nom }} ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                            Aucun personnel trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2 border-top d-flex justify-content-between align-items-center">
            <small class="text-muted">
                {{ $personnels->firstItem() ?? 0 }} à {{ $personnels->lastItem() ?? 0 }}
                sur {{ $personnels->total() }} résultat(s)
            </small>
            {{ $personnels->links() }}
        </div>
    </div>
</div>
 
{{-- ═══════════════════════════════════════════════════════════════
     MODAL : CRÉER UN PERSONNEL
     ═══════════════════════════════════════════════════════════════ --}}
@can('creer personnel')
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('personnels.store') }}">
                @csrf
                <div class="modal-header" style="background:#1F3864">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-person-plus me-2"></i>Créer un compte personnel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Identité --}}
                        <div class="col-12">
                            <div class="fw-semibold text-muted small text-uppercase mb-1">
                                <i class="bi bi-person me-1"></i>Identité
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control" required
                                   placeholder="SARBE BANA">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" class="form-control" required
                                   placeholder="Léontine Brelle">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom affiché dans la navbar</label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Léontine SARBE">
                        </div>
 
                        <div class="col-12"><hr class="my-0"></div>
 
                        {{-- Compte --}}
                        <div class="col-12">
                            <div class="fw-semibold text-muted small text-uppercase mb-1">
                                <i class="bi bi-shield-lock me-1"></i>Compte de connexion
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control" required
                                   placeholder="agent@cenadi.cm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Matricule <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="matricule" class="form-control" required
                                   placeholder="24GLO56IU">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Mot de passe <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" id="createMdp"
                                       class="form-control" required minlength="8"
                                       placeholder="Minimum 8 caractères">
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="toggleMdp('createMdp','createEye')">
                                    <i class="bi bi-eye" id="createEye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Rôle <span class="text-danger">*</span>
                            </label>
                            <select name="role" class="form-select" required>
                                <option value="personnel">Personnel</option>
                                <option value="chef_division">Chef de Division</option>
                                <option value="administrateur">Administrateur</option>
                            </select>
                        </div>
 
                        <div class="col-12"><hr class="my-0"></div>
 
                        {{-- Affectation --}}
                        <div class="col-12">
                            <div class="fw-semibold text-muted small text-uppercase mb-1">
                                <i class="bi bi-building me-1"></i>Affectation organisationnelle
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Division</label>
                            <select name="division_id" class="form-select"
                                    onchange="filtrerServicesCreate(this.value)">
                                <option value="">— Aucune —</option>
                                @foreach($divisions as $d)
                                <option value="{{ $d->id }}">{{ $d->code }} — {{ $d->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Service</label>
                            <select name="service_id" id="createServiceSelect" class="form-select"
                                    onchange="filtrerBureauxCreate(this.value)">
                                <option value="">— Aucun —</option>
                                @foreach($services as $s)
                                <option value="{{ $s->id }}">{{ $s->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bureau</label>
                            <select name="bureau_id" id="createBureauSelect" class="form-select">
                                <option value="">— Aucun —</option>
                                @foreach($bureaux as $b)
                                <option value="{{ $b->id }}">{{ $b->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-check me-1"></i>Créer le compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
 
{{-- ═══════════════════════════════════════════════════════════════
     MODAL : MODIFIER UN PERSONNEL
     ═══════════════════════════════════════════════════════════════ --}}
@can('modifier personnel')
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="formEditPer">
                @csrf
                @method('PUT')
                <div class="modal-header" style="background:#fd7e14">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-pencil me-2"></i>Modifier le personnel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- Identité --}}
                        <div class="col-12">
                            <div class="fw-semibold text-muted small text-uppercase mb-1">
                                <i class="bi bi-person me-1"></i>Identité
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" id="editPerNom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" id="editPerPrenom" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="telephone" id="editPerTel" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom affiché (navbar)</label>
                            <input type="text" name="name" id="editPerName" class="form-control">
                        </div>
 
                        <div class="col-12"><hr class="my-0"></div>
 
                        {{-- Compte --}}
                        <div class="col-12">
                            <div class="fw-semibold text-muted small text-uppercase mb-1">
                                <i class="bi bi-shield-lock me-1"></i>Compte de connexion
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editPerEmail" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Matricule <span class="text-danger">*</span></label>
                            <input type="text" name="matricule" id="editPerMatricule" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nouveau mot de passe
                                <small class="text-muted fw-normal">(laisser vide pour ne pas changer)</small>
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" id="editMdp"
                                       class="form-control" minlength="8"
                                       placeholder="Laisser vide pour conserver">
                                <button type="button" class="btn btn-outline-secondary"
                                        onclick="toggleMdp('editMdp','editEye')">
                                    <i class="bi bi-eye" id="editEye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                            <select name="role" id="editPerRole" class="form-select" required>
                                <option value="personnel">Personnel</option>
                                <option value="chef_division">Chef de Division</option>
                                <option value="administrateur">Administrateur</option>
                            </select>
                        </div>
 
                        <div class="col-12"><hr class="my-0"></div>
 
                        {{-- Affectation --}}
                        <div class="col-12">
                            <div class="fw-semibold text-muted small text-uppercase mb-1">
                                <i class="bi bi-building me-1"></i>Affectation organisationnelle
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Division</label>
                            <select name="division_id" id="editPerDiv" class="form-select">
                                <option value="">— Aucune —</option>
                                @foreach($divisions as $d)
                                <option value="{{ $d->id }}">{{ $d->code }} — {{ $d->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Service</label>
                            <select name="service_id" id="editPerSvc" class="form-select">
                                <option value="">— Aucun —</option>
                                @foreach($services as $s)
                                <option value="{{ $s->id }}">{{ $s->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bureau</label>
                            <select name="bureau_id" id="editPerBur" class="form-select">
                                <option value="">— Aucun —</option>
                                @foreach($bureaux as $b)
                                <option value="{{ $b->id }}">{{ $b->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="bi bi-save me-1"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
 
{{-- ═══════════════════════════════════════════════════════════════
     MODAL : ACTIVER / DÉSACTIVER UN COMPTE
     ═══════════════════════════════════════════════════════════════ --}}
@can('modifier personnel')
<div class="modal fade" id="modalToggle" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="formToggle">
                @csrf @method('PATCH')
                <div class="modal-header" id="toggleHeader">
                    <h6 class="modal-title fw-bold" id="toggleTitre"></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi fs-1 d-block mb-3" id="toggleIcone"></i>
                    <p class="mb-0" id="toggleMessage"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn" id="toggleBtn">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
 
@push('scripts')
<script>
// ── Alimenter la modale Modifier ──────────────────────────────────
document.querySelectorAll('.btn-edit-per').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const d = JSON.parse(this.dataset.per);
 
        // Action du formulaire
        document.getElementById('formEditPer').action = '/personnels/' + d.id;
 
        // Remplir les champs
        document.getElementById('editPerNom').value      = d.nom         ?? '';
        document.getElementById('editPerPrenom').value   = d.prenom      ?? '';
        document.getElementById('editPerTel').value      = d.telephone   ?? '';
        document.getElementById('editPerName').value     = d.name        ?? '';
        document.getElementById('editPerEmail').value    = d.email       ?? '';
        document.getElementById('editPerMatricule').value= d.matricule   ?? '';
        document.getElementById('editPerRole').value     = d.role        ?? 'personnel';
        document.getElementById('editPerDiv').value      = d.division_id ?? '';
        document.getElementById('editPerSvc').value      = d.service_id  ?? '';
        document.getElementById('editPerBur').value      = d.bureau_id   ?? '';
 
        // Vider le mot de passe (sécurité)
        document.getElementById('editMdp').value = '';
    });
});
 
// ── Alimenter la modale Toggle Activer/Désactiver ─────────────────
document.querySelectorAll('[data-bs-target="#modalToggle"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const id     = this.dataset.id;
        const nom    = this.dataset.nom;
        const actif  = this.dataset.statut === '1';
 
        document.getElementById('formToggle').action = '/personnels/' + id + '/toggle';
 
        if (actif) {
            // Désactiver
            document.getElementById('toggleHeader').style.background = '#dc3545';
            document.getElementById('toggleHeader').classList.add('text-white');
            document.getElementById('toggleTitre').textContent   = 'Désactiver le compte';
            document.getElementById('toggleIcone').className     = 'bi bi-pause-circle-fill fs-1 d-block mb-3 text-danger';
            document.getElementById('toggleMessage').innerHTML   =
                `Voulez-vous désactiver le compte de <strong>${nom}</strong> ?<br>
                 <small class="text-muted">Il ne pourra plus se connecter à la plateforme.</small>`;
            document.getElementById('toggleBtn').className       = 'btn btn-danger';
            document.getElementById('toggleBtn').textContent     = 'Désactiver';
        } else {
            // Activer
            document.getElementById('toggleHeader').style.background = '#198754';
            document.getElementById('toggleHeader').classList.add('text-white');
            document.getElementById('toggleTitre').textContent   = 'Activer le compte';
            document.getElementById('toggleIcone').className     = 'bi bi-play-circle-fill fs-1 d-block mb-3 text-success';
            document.getElementById('toggleMessage').innerHTML   =
                `Voulez-vous activer le compte de <strong>${nom}</strong> ?<br>
                 <small class="text-muted">Il pourra à nouveau se connecter à la plateforme.</small>`;
            document.getElementById('toggleBtn').className       = 'btn btn-success';
            document.getElementById('toggleBtn').textContent     = 'Activer';
        }
    });
});
 
// ── Afficher/masquer mot de passe ─────────────────────────────────
function toggleMdp(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type    = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type    = 'password';
        icon.className = 'bi bi-eye';
    }
}

    const TOUS_SERVICES = @json($services->map(fn($s) => ['id' => $s->id, 'nom' => $s->nom, 'division_id' => $s->division_id]));
    const TOUS_BUREAUX = @json($bureaux->map(fn($b) => ['id' => $b->id, 'nom' => $b->nom, 'service_id' => $b->service_id]));

// ── Filtrer les services selon la division choisie ────────────────
function filtrerServicesCreate(divisionId) {
    const selectService = document.getElementById('createServiceSelect');
    const selectBureau  = document.getElementById('createBureauSelect');

    // Vider les deux selects
    selectService.innerHTML = '<option value="">— Aucun —</option>';
    selectBureau.innerHTML  = '<option value="">— Aucun —</option>';

    if (!divisionId) return;

    // Filtrer et remplir les services
    const servicesFiltres = TOUS_SERVICES.filter(
        s => String(s.division_id) === String(divisionId)
    );

    servicesFiltres.forEach(function(s) {
        const opt = document.createElement('option');
        opt.value       = s.id;
        opt.textContent = s.nom;
        selectService.appendChild(opt);
    });
}

// ── Filtrer les bureaux selon le service choisi ───────────────────
function filtrerBureauxCreate(serviceId) {
    const selectBureau = document.getElementById('createBureauSelect');

    // Vider les bureaux
    selectBureau.innerHTML = '<option value="">— Aucun —</option>';

    if (!serviceId) return;

    // Filtrer et remplir les bureaux
    const bureauxFiltres = TOUS_BUREAUX.filter(
        b => String(b.service_id) === String(serviceId)
    );

    bureauxFiltres.forEach(function(b) {
        const opt = document.createElement('option');
        opt.value       = b.id;
        opt.textContent = b.nom;
        selectBureau.appendChild(opt);
    });
}

// ── Toggle mot de passe ───────────────────────────────────────────
function toggleMdp(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type     = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type     = 'password';
        icon.className = 'bi bi-eye';
    }
}

</script>
@endpush
 
@endsection
 