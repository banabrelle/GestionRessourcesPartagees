@extends('layouts.app')
@section('title', $personnel->prenom . ' ' . $personnel->nom)
 
@section('content')
 
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0" style="color:#1F3864">
            <i class="bi bi-person-circle me-2"></i>
            {{ $personnel->prenom }} {{ $personnel->nom }}
        </h4>
        <small class="text-muted">
            <code>{{ $personnel->matricule }}</code>
            &mdash; {{ ucfirst(str_replace('_',' ', $personnel->user?->getRoleNames()->first() ?? '—')) }}
        </small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('personnels.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
        @can('modifier personnel')
        <button class="btn btn-warning btn-sm text-white btn-edit-per"
                data-bs-toggle="modal"
                data-bs-target="#modalEditShow"
                data-per='{{ json_encode([
                    "id"          => $personnel->id,
                    "nom"         => $personnel->nom,
                    "prenom"      => $personnel->prenom,
                    "telephone"   => $personnel->telephone,
                    "division_id" => $personnel->division_id,
                    "service_id"  => $personnel->service_id,
                    "bureau_id"   => $personnel->bureau_id,
                    "email"       => $personnel->email,
                    "matricule"   => $personnel->matricule,
                    "role"        => $personnel->user?->getRoleNames()->first(),
                    "name"        => $personnel->user?->name,
                ]) }}'>
            <i class="bi bi-pencil me-1"></i>Modifier
        </button>
        @endcan
    </div>
</div>
 
<div class="row g-3">
 
    {{-- ── Colonne gauche : profil ── --}}
    <div class="col-lg-4">
 
        {{-- Carte avatar + infos rapides --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body text-center py-4">
                @if($personnel->photo)
                    <img src="{{ Storage::url($personnel->photo) }}"
                         class="rounded-circle mb-3 border border-3"
                         style="width:100px;height:100px;object-fit:cover;border-color:#1F3864!important">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center
                                fw-bold text-white mx-auto mb-3"
                         style="width:100px;height:100px;font-size:36px;background:#1F3864">
                        {{ strtoupper(substr($personnel->prenom,0,1).substr($personnel->nom,0,1)) }}
                    </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $personnel->prenom }} {{ $personnel->nom }}</h5>
                <div class="text-muted small mb-2">{{ $personnel->user?->email }}</div>
                @php
                    $role = $personnel->user?->getRoleNames()->first() ?? '—';
                    $roleColor = match($role) {
                        'administrateur' => 'danger',
                        'chef_division'  => 'warning text-dark',
                        'personnel'      => 'info text-dark',
                        default          => 'secondary',
                    };
                @endphp
                <span class="badge bg-{{ $roleColor }} mb-2">
                    {{ ucfirst(str_replace('_',' ',$role)) }}
                </span>
                <br>
                @if($personnel->statut)
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Compte actif
                    </span>
                @else
                    <span class="badge bg-danger">
                        <i class="bi bi-x-circle me-1"></i>Compte inactif
                    </span>
                @endif
            </div>
        </div>
 
        {{-- Informations de contact --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-info-circle me-2" style="color:#1F3864"></i>Informations
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Matricule</span>
                    <code class="fw-bold">{{ $personnel->matricule }}</code>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Téléphone</span>
                    <span class="small">{{ $personnel->telephone ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Division</span>
                    <span class="badge" style="background:#1F3864">
                        {{ $personnel->division?->code ?? '—' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Service</span>
                    <span class="small text-end" style="max-width:160px">
                        {{ $personnel->service?->nom ?? '—' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Bureau</span>
                    <span class="small">{{ $personnel->bureau?->nom ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Membre depuis</span>
                    <span class="small">{{ $personnel->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
 
        {{-- Statistiques rapides --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-bar-chart me-2" style="color:#1F3864"></i>Activité
            </div>
            <div class="card-body">
                @php
                    $totalRes  = $personnel->reservations->count();
                    $valides   = $personnel->reservations->where('statut','valide_admin')->count();
                    $enAttente = $personnel->reservations->whereIn('statut',['en_attente','valide_chef'])->count();
                    $rejetes   = $personnel->reservations->whereIn('statut',['rejete_chef','rejete_admin'])->count();
                @endphp
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Total réservations</span>
                    <span class="badge bg-primary">{{ $totalRes }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Validées</span>
                    <span class="badge bg-success">{{ $valides }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">En attente</span>
                    <span class="badge bg-warning text-dark">{{ $enAttente }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Rejetées</span>
                    <span class="badge bg-danger">{{ $rejetes }}</span>
                </div>
                @if($totalRes > 0)
                <div class="progress mt-3" style="height:8px" title="Taux de validation">
                    <div class="progress-bar bg-success"
                         style="width:{{ round($valides/$totalRes*100) }}%"></div>
                    <div class="progress-bar bg-warning"
                         style="width:{{ round($enAttente/$totalRes*100) }}%"></div>
                    <div class="progress-bar bg-danger"
                         style="width:{{ round($rejetes/$totalRes*100) }}%"></div>
                </div>
                <small class="text-muted">
                    Taux de validation : {{ round($valides/$totalRes*100) }}%
                </small>
                @endif
            </div>
        </div>
    </div>
 
    {{-- ── Colonne droite : historique ── --}}
    <div class="col-lg-8">
 
        {{-- Réservations récentes --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-calendar-check me-2" style="color:#1F3864"></i>
                    Historique des réservations
                </h6>
                <a href="{{ route('reservations.index', ['personnel_id' => $personnel->id]) }}"
                   class="btn btn-sm btn-outline-primary">
                    Voir tout
                </a>
            </div>
            <div style="max-height:350px;overflow-y:auto">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-dark" style="position:sticky;top:0">
                        <tr>
                            <th>Ressource</th>
                            <th>Type</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Durée</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personnel->reservations->sortByDesc('created_at')->take(15) as $r)
                        <tr>
                            <td>
                                <span class="fw-semibold small">
                                    {{ Str::limit($r->ressource?->nom ?? '—', 25) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $r->ressource?->type_ressource === 'materiel' ? 'bg-primary' : 'bg-info text-dark' }}"
                                      style="font-size:10px">
                                    {{ ucfirst($r->ressource?->type_ressource ?? '—') }}
                                </span>
                            </td>
                            <td><small>{{ $r->date_debut?->format('d/m/Y') }}</small><br>
                                <small class="text-muted">{{ $r->date_debut?->format('H:i') }}</small></td>
                            <td><small>{{ $r->date_fin?->format('d/m/Y') }}</small><br>
                                <small class="text-muted">{{ $r->date_fin?->format('H:i') }}</small></td>
                            <td><span class="badge bg-light text-dark border">{{ $r->duree_heures }}h</span></td>
                            <td>
                                <span class="badge bg-{{ $r->badge_color }}" style="font-size:10px">
                                    {{ $r->statut_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('reservations.show',$r) }}"
                                   class="btn btn-xs btn-outline-info btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x d-block mb-1 fs-3 opacity-25"></i>
                                Aucune réservation
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
 
        {{-- Ressources actuellement empruntées --}}
        @php
            $empruntsActifs = $personnel->reservations
                ->where('statut','valide_admin')
                ->filter(fn($r) => $r->date_fin?->isFuture())
                ->sortBy('date_fin');
        @endphp
        @if($empruntsActifs->count() > 0)
        <div class="card shadow-sm border-0 border-start border-success border-3 mb-3">
            <div class="card-header bg-success-subtle">
                <h6 class="mb-0 fw-semibold text-success">
                    <i class="bi bi-box-seam me-2"></i>
                    Ressources actuellement empruntées ({{ $empruntsActifs->count() }})
                </h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($empruntsActifs as $r)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <div class="fw-semibold small">{{ $r->ressource?->nom }}</div>
                            <small class="text-muted">
                                Retour prévu : {{ $r->date_fin?->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge bg-success">En cours</span>
                            @if($r->bonEmprunt)
                            <a href="{{ route('bons.telecharger', $r->bonEmprunt) }}"
                               class="btn btn-sm btn-outline-danger" title="Bon PDF">
                                <i class="bi bi-file-pdf"></i>
                            </a>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
 
        {{-- Informations division --}}
        @if($personnel->division)
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-diagram-3 me-2" style="color:#1F3864"></i>
                Division : {{ $personnel->division->nom }}
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 text-center">
                        <div class="rounded-3 p-3" style="background:#e8f4fd">
                            <div class="fs-3 fw-bold" style="color:#1F3864">
                                {{ $personnel->division->code }}
                            </div>
                            <small class="text-muted">Code division</small>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <p class="text-muted small mb-2">{{ $personnel->division->description ?? 'Aucune description.' }}</p>
                        @if($personnel->division->chef)
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold"
                                 style="width:32px;height:32px;font-size:12px">
                                {{ strtoupper(substr($personnel->division->chef->prenom,0,1).substr($personnel->division->chef->nom,0,1)) }}
                            </div>
                            <div>
                                <div class="small fw-semibold">
                                    Chef : {{ $personnel->division->chef->prenom }} {{ $personnel->division->chef->nom }}
                                </div>
                                <small class="text-muted">{{ $personnel->division->chef->telephone ?? '' }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
 
    </div>
</div>
 
{{-- MODAL MODIFIER (dans la page show) --}}
@can('modifier personnel')
<div class="modal fade" id="modalEditShow" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="formEditPerShow">
                @csrf @method('PUT')
                <div class="modal-header" style="background:#fd7e14">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-pencil me-2"></i>Modifier — {{ $personnel->prenom }} {{ $personnel->nom }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom *</label>
                            <input type="text" name="nom" id="showEditNom" class="form-control" required
                                   value="{{ $personnel->nom }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom *</label>
                            <input type="text" name="prenom" id="showEditPrenom" class="form-control" required
                                   value="{{ $personnel->prenom }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="telephone" class="form-control"
                                   value="{{ $personnel->telephone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email *</label>
                            <input type="email" name="email" class="form-control" required
                                   value="{{ $personnel->email }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Matricule *</label>
                            <input type="text" name="matricule" class="form-control" required
                                   value="{{ $personnel->matricule }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rôle *</label>
                            <select name="role" class="form-select" required>
                                @foreach(['personnel','chef_division','administrateur'] as $r)
                                <option value="{{ $r }}"
                                    {{ $personnel->user?->getRoleNames()->first() === $r ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_',' ',$r)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nouveau mot de passe
                                <small class="text-muted fw-normal">(vide = inchangé)</small>
                            </label>
                            <input type="password" name="password" class="form-control"
                                   minlength="8" placeholder="Laisser vide pour conserver">
                        </div>
                        {{-- partie a probleme --}}
                        {{-- <div class="col-md-4">
                            <label class="form-label fw-semibold">Division</label>
                            <select name="division_id" class="form-select">
                                <option value="">— Aucune —</option>
                                @foreach($division as $d)
                                <option value="{{ $d->id }}"
                                    {{ $personnel->division_id == $d->id ? 'selected' : '' }}>
                                    {{ $d->code }} — {{ $d->nom }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Service</label>
                            <select name="service_id" class="form-select">
                                <option value="">— Aucun —</option>
                                @foreach($services as $s)
                                <option value="{{ $s->id }}"
                                    {{ $personnel->service_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->nom }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bureau</label>
                            <select name="bureau_id" class="form-select">
                                <option value="">— Aucun —</option>
                                @foreach($bureaux as $b)
                                <option value="{{ $b->id }}"
                                    {{ $personnel->bureau_id == $b->id ? 'selected' : '' }}>
                                    {{ $b->nom }}
                                </option>
                                @endforeach
                            </select>
                        </div> --}} 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="bi bi-save me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
 
@push('scripts')
<script>
document.querySelectorAll('.btn-edit-per').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const d = JSON.parse(this.dataset.per);
        document.getElementById('formEditPerShow').action = '/personnels/' + d.id;
    });
});
</script>
@endpush
 
@endsection