@extends('layouts.app')

@section('title', 'Journal du module aide à la décision')

@section('content')
<style>
  body {
    background-color: #E3F3FC;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0" style="color:#1F3864">
        <i class="bi bi-robot me-2"></i>Journal des Suggestions Alternatives
    </h4>

    {{-- Bouton conflits — badge rouge si conflits détectés --}}
    @if($statsConflits['total_conflits'] > 0)
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalConflits">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ $statsConflits['total_conflits'] }} Conflit(s) détecté(s)
    </button>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── KPIs ─────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:rgba(111,66,193,0.1)">
                    <i class="bi bi-robot fs-4" style="color:#6f42c1"></i>
                </div>
                <div class="fs-2 fw-bold" style="color:#6f42c1">{{ $stats['total'] }}</div>
                <div class="text-muted small">Suggestions totales</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:rgba(25,135,84,0.1)">
                    <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                </div>
                <div class="fs-2 fw-bold text-success">{{ $stats['acceptees'] }}</div>
                <div class="text-muted small">Acceptées</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:rgba(255,193,7,0.1)">
                    <i class="bi bi-graph-up-arrow fs-4 text-warning"></i>
                </div>
                <div class="fs-2 fw-bold text-warning">{{ $stats['taux_acceptation'] }}%</div>
                <div class="text-muted small">Taux d'acceptation</div>
                <div class="progress mt-2" style="height:4px">
                    <div class="progress-bar bg-warning" style="width:{{ $stats['taux_acceptation'] }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 text-center h-100">
            <div class="card-body">
                <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                     style="width:50px;height:50px;background:rgba(220,53,69,0.1)">
                    <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
                </div>
                <div class="fs-2 fw-bold text-danger">{{ $statsConflits['total_conflits'] }}</div>
                <div class="text-muted small">Conflits détectés</div>
                @if($statsConflits['total_conflits'] > 0)
                <button class="btn btn-sm btn-outline-danger mt-2"
                        data-bs-toggle="modal" data-bs-target="#modalConflits">
                    Voir détails
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Distribution par type ───────────────────────────────────── --}}
@if($parType->count() > 0)
<div class="row g-3 mb-4">
    @foreach($parType as $pt)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:45px;height:45px;background:rgba(46,117,182,0.1);flex-shrink:0">
                    <i class="bi bi-{{ $pt->type_suggestion === 'creneau' ? 'clock' : 'box-seam' }} text-primary fs-5"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold text-capitalize">{{ $pt->type_suggestion }}</div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">{{ $pt->total }} suggestion(s)</small>
                        <small class="text-success">{{ $pt->acceptees }} acceptée(s)</small>
                    </div>
                    <div class="progress mt-1" style="height:4px">
                        <div class="progress-bar bg-primary"
                             style="width:{{ $pt->total > 0 ? round($pt->acceptees / $pt->total * 100) : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:45px;height:45px;background:rgba(25,135,84,0.1);flex-shrink:0">
                    <i class="bi bi-clock-history text-success fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold">Temps gagné</div>
                    <div class="fs-5 fw-bold text-success">
                        {{ round($stats['temps_total_gagne'] / 60, 1) }}h
                    </div>
                    <small class="text-muted">{{ $stats['temps_total_gagne'] }} minutes au total</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ── Filtres ──────────────────────────────────────────────────── --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Tous les types</option>
                    <option value="creneau"   {{ request('type') === 'creneau'   ? 'selected' : '' }}>Créneau</option>
                    <option value="ressource" {{ request('type') === 'ressource' ? 'selected' : '' }}>Ressource</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="statut" class="form-select form-select-sm">
                    <option value="">Tous les statuts</option>
                    <option value="acceptee"  {{ request('statut') === 'acceptee'  ? 'selected' : '' }}>Acceptée</option>
                    <option value="ignoree"   {{ request('statut') === 'ignoree'   ? 'selected' : '' }}>Ignorée</option>
                    <option value="en_attente"{{ request('statut') === 'en_attente'? 'selected' : '' }}>En attente</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                    <i class="bi bi-search me-1"></i>Filtrer
                </button>
                <a href="{{ route('ai.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ── Liste des suggestions ────────────────────────────────────── --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <span class="fw-semibold">{{ $suggestions->total() }} suggestion(s)</span>
        <span class="badge bg-secondary">Page {{ $suggestions->currentPage() }}</span>
    </div>
    <div class="card-body p-0">
        <div style="max-height: calc(100vh - 500px); overflow-y: auto;">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-dark" style="position:sticky;top:0;z-index:1;">
                    <tr>
                        <th>#</th>
                        <th>Personnel</th>
                        <th>Type</th>
                        <th>Données suggérées</th>
                        <th>Créé le</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suggestions as $s)
                    <tr>
                        <td><small class="text-muted">{{ $s->id }}</small></td>
                        <td>
                            <strong>{{ $s->personnel?->prenom }} {{ $s->personnel?->nom }}</strong><br>
                            <small class="text-muted">{{ $s->personnel?->division?->code ?? '—' }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $s->type_suggestion === 'creneau' ? 'bg-info text-dark' : 'bg-primary' }}">
                                <i class="bi bi-{{ $s->type_suggestion === 'creneau' ? 'clock' : 'box-seam' }} me-1"></i>
                                {{ ucfirst($s->type_suggestion) }}
                            </span>
                        </td>
                        <td>
                            @if($s->suggestion_data)
                                @php $data = $s->suggestion_data; @endphp
                                @if(!empty($data['creneaux']))
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ count($data['creneaux']) }} créneau(x) suggéré(s)
                                    </small><br>
                                    @foreach(array_slice($data['creneaux'], 0, 2) as $c)
                                    <span class="badge bg-light text-dark border me-1" style="font-size:10px">
                                        {{ $c['label'] ?? ($c['debut'] ?? '—') }}
                                    </span>
                                    @endforeach
                                @endif
                                @if(!empty($data['ressources']))
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-box-seam me-1"></i>
                                        {{ count($data['ressources']) }} ressource(s) alternative(s)
                                    </small>
                                @endif
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </td>
                        <td>
                            <small>{{ $s->created_at->format('d/m/Y') }}</small><br>
                            <small class="text-muted">{{ $s->created_at->format('H:i') }}</small>
                        </td>
                        <td>
                            @if($s->acceptee)
                                <span class="badge bg-success"><i class="bi bi-check me-1"></i>Acceptée</span>
                            @elseif($s->ignoree)
                                <span class="badge bg-secondary"><i class="bi bi-dash me-1"></i>Ignorée</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>En attente</span>
                            @endif
                            @if($s->temps_gagne_minutes)
                            <br><small class="text-success">+{{ $s->temps_gagne_minutes }}min</small>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-info"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDetailSuggestion"
                                        onclick="afficherDetail({{ json_encode($s) }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if(!$s->acceptee && !$s->ignoree)
                                <form method="POST" action="{{ route('ai.accepter', $s) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-outline-success" title="Accepter">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('ai.ignorer', $s) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-outline-secondary" title="Ignorer">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-robot fs-1 d-block mb-3 opacity-25"></i>
                            Aucune suggestion IA enregistrée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $suggestions->links() }}
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODALE : CONFLITS DÉTECTÉS                                    --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalConflits" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Conflits de réservation détectés
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <div>
                        <strong>{{ $statsConflits['total_conflits'] }} conflit(s)</strong> ont été détectés
                        sur des réservations. Voici les ressources les plus concernées.
                    </div>
                </div>

                @if($statsConflits['par_ressource']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-danger">
                            <tr>
                                <th>Ressource</th>
                                <th class="text-center">Nb conflits</th>
                                <th class="text-center">Priorité</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statsConflits['par_ressource'] as $conflit)
                            <tr>
                                <td>
                                    <i class="bi bi-box-seam me-2 text-danger"></i>
                                    <strong>{{ $conflit->ressource?->nom ?? 'Ressource #'.$conflit->ressource_id }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger fs-6">{{ $conflit->nb }}</span>
                                </td>
                                <td class="text-center">
                                    @if($conflit->nb >= 3)
                                        <span class="badge bg-danger">Haute</span>
                                    @elseif($conflit->nb == 2)
                                        <span class="badge bg-warning text-dark">Moyenne</span>
                                    @else
                                        <span class="badge bg-secondary">Faible</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('reservations.index', ['ressource_id' => $conflit->ressource_id]) }}"
                                       class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-eye me-1"></i>Voir
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center py-3">Aucun détail disponible.</p>
                @endif

            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="{{ route('reservations.index') }}" class="btn btn-danger">
                    <i class="bi bi-calendar-check me-1"></i>Gérer les réservations
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- MODALE : DÉTAIL D'UNE SUGGESTION                              --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailSuggestion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white"
                 style="background:linear-gradient(135deg,#1F3864,#6f42c1);">
                <h5 class="modal-title">
                    <i class="bi bi-robot me-2"></i>Détail de la suggestion IA
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenuDetailSuggestion">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- Ouvrir automatiquement la modale conflits si présents --}}
@if($statsConflits['total_conflits'] > 0 && !request()->has('no_conflit_modal'))
@push('scripts')
<script>
    // Ouvrir la modale conflits automatiquement à la première visite
    document.addEventListener('DOMContentLoaded', function () {
        const alreadySeen = sessionStorage.getItem('conflits_modal_seen_{{ now()->format("Ymd") }}');
        if (!alreadySeen) {
            const modal = new bootstrap.Modal(document.getElementById('modalConflits'));
            modal.show();
            sessionStorage.setItem('conflits_modal_seen_{{ now()->format("Ymd") }}', '1');
        }
    });
</script>
@endpush
@endif

@push('scripts')
<script>
function afficherDetail(suggestion) {
    const container = document.getElementById('contenuDetailSuggestion');
    const data = suggestion.suggestion_data;

    let html = `
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="p-3 rounded" style="background:#f8f9fa">
                    <div class="text-muted small mb-1">Personnel</div>
                    <div class="fw-semibold">${suggestion.personnel?.prenom ?? ''} ${suggestion.personnel?.nom ?? '—'}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 rounded" style="background:#f8f9fa">
                    <div class="text-muted small mb-1">Type</div>
                    <span class="badge bg-info text-dark">${suggestion.type_suggestion}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 rounded" style="background:#f8f9fa">
                    <div class="text-muted small mb-1">Statut</div>
                    ${suggestion.acceptee
                        ? '<span class="badge bg-success">Acceptée</span>'
                        : suggestion.ignoree
                            ? '<span class="badge bg-secondary">Ignorée</span>'
                            : '<span class="badge bg-warning text-dark">En attente</span>'}
                </div>
            </div>
        </div>`;

    if (data) {
        // Créneaux suggérés
        if (data.creneaux && data.creneaux.length > 0) {
            html += `<h6 class="fw-bold mb-2"><i class="bi bi-clock me-2 text-info"></i>Créneaux suggérés</h6>
                     <div class="row g-2 mb-3">`;
            data.creneaux.forEach((c, i) => {
                const score = Math.round((c.score || 0) * 100);
                html += `
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-3">
                                <div class="badge bg-primary mb-2">#${i+1}</div>
                                <div class="fw-semibold small">${c.label || c.debut}</div>
                                <div class="text-muted" style="font-size:11px">→ ${c.fin}</div>
                                <div class="progress mt-2" style="height:4px">
                                    <div class="progress-bar bg-success" style="width:${score}%"></div>
                                </div>
                                <small class="text-muted">Score: ${score}%</small>
                            </div>
                        </div>
                    </div>`;
            });
            html += `</div>`;
        }

        // Ressources alternatives
        if (data.ressources && data.ressources.length > 0) {
            html += `<h6 class="fw-bold mb-2"><i class="bi bi-box-seam me-2 text-primary"></i>Ressources alternatives</h6>
                     <div class="row g-2">`;
            data.ressources.forEach(r => {
                const score = Math.round((r.score || 0) * 100);
                html += `
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-3">
                                <div class="fw-semibold">${r.nom}</div>
                                <small class="text-muted d-block">${r.division} — ${r.localisation}</small>
                                ${r.marque ? `<small class="text-muted">${r.marque}</small>` : ''}
                                ${r.tags ? `<div class="mt-1"><span class="badge bg-light text-dark border" style="font-size:10px">${r.tags}</span></div>` : ''}
                                <div class="progress mt-2" style="height:4px">
                                    <div class="progress-bar bg-primary" style="width:${score}%"></div>
                                </div>
                                <small class="text-muted">Similarité: ${score}%</small>
                            </div>
                        </div>
                    </div>`;
            });
            html += `</div>`;
        }

        if ((!data.creneaux || data.creneaux.length === 0) &&
            (!data.ressources || data.ressources.length === 0)) {
            html += `<div class="text-center text-muted py-3">
                        <i class="bi bi-inbox fs-1 opacity-25 d-block mb-2"></i>
                        Aucune donnée de suggestion disponible
                     </div>`;
        }
    } else {
        html += `<div class="text-center text-muted py-3">Aucune donnée disponible</div>`;
    }

    container.innerHTML = html;
}
</script>
@endpush

@endsection