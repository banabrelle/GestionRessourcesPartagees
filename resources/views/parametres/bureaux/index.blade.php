<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-door-open-fill text-primary me-2"></i>Gestion des Bureaux
        </h4>
    </div>
    <button type="button" class="btn btn-primary"
            data-bs-toggle="modal" data-bs-target="#modalCreerBureau">
        <i class="bi bi-plus-circle-fill me-2"></i>Nouveau Bureau
    </button>
</div>
 
<div id="alertGlobalBur"></div>
 
{{-- FILTRES --}}
<form method="GET" action="{{ route('parametres.index') }}" class="row g-2 mb-3">
    <input type="hidden" name="tab" value="bureaux">
    <div class="col-md-3">
        <select name="division" class="form-select form-select-sm">
            <option value="">Toutes divisions</option>
            @foreach($toutsDivisions as $div)
            <option value="{{ $div->id }}" {{ request('division') == $div->id ? 'selected' : '' }}>
                {{ $div->code }} - {{ $div->nom }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="service" class="form-select form-select-sm">
            <option value="">Tous services</option>
            @foreach($tousServices as $srv)
            <option value="{{ $srv->id }}" {{ request('service') == $srv->id ? 'selected' : '' }}>
                {{ $srv->code }} - {{ $srv->nom }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Rechercher..." value="{{ request('search') }}">
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-secondary btn-sm flex-fill">
            <i class="bi bi-search me-1"></i>Filtrer
        </button>
        <a href="{{ route('parametres.index', ['tab' => 'bureaux']) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x"></i>
        </a>
    </div>
</form>
 
{{-- TABLEAU --}}
<div class="card shadow-sm border-0">
    <div style="max-height: calc(100vh - 420px); overflow-y: auto;">
        <table class="table table-sm table-hover table-striped align-middle mb-0">
            <thead class="table-dark" style="position:sticky;top:0;z-index:1;">
                <tr>
                    <th>Nom du Bureau</th>
                    <th>Division</th>
                    <th class="text-center">Personnel</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($bureaux as $bureau)
            <tr id="row-bur-{{ $bureau->id }}">
                <td>
                    <div class="fw-semibold">{{ $bureau->nom }}</div>
                    @if($bureau->description)
                    <small class="text-muted">{{ Str::limit($bureau->description, 50) }}</small>
                    @endif
                </td>
                 <td>
                    <span class="badge bg-primary">
                        {{ $bureau->service?->division?->code ?? '—' }}
                    </span>
                </td>
                {{-- <td><i class="bi bi-geo-alt me-1 text-muted"></i>{{ $bureau->localisation ?? '—' }}</td>  --}}
                <td class="text-center">
                    <span class="badge bg-secondary">{{ $bureau->personnels_count ?? 0 }}</span>
                </td>
                <td class="text-end">
                    <button class="btn btn-warning btn-sm py-0 px-2 me-1"
                            data-bs-toggle="modal" data-bs-target="#modalModifierBureau"
                            onclick="chargerBureau({{ json_encode($bureau) }})">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="btn btn-danger btn-sm py-0 px-2"
                            onclick="supprimerBureau({{ $bureau->id }}, '{{ addslashes($bureau->nom) }}')">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-5">
                    <i class="bi bi-door-open fs-1 d-block mb-3 opacity-25"></i>
                    Aucun bureau trouve
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $bureaux->appends(['tab' => 'bureaux'])->links() }}
    </div>
</div>
 
{{-- MODALE CREER BUREAU --}}
<div class="modal fade" id="modalCreerBureau" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white"
                 style="background:linear-gradient(135deg,#1F3864,#2E75B6);">
                <h5 class="modal-title">
                    <i class="bi bi-door-open-fill me-2"></i>Nouveau Bureau
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="alertCreerBureau"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Division</label>
                        <select id="creer_bur_division" class="form-select"
                            onchange="chargerServicesDivision(this.value, 'creer_bur_service')">
                            <option value="">-- Choisir une division --</option>
                            @foreach($toutsDivisions as $div)
                                <option value="{{ $div->id }}">{{ $div->code }} - {{ $div->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Service <span class="text-danger">*</span></label>
                        <select id="creer_bur_service" class="form-select" required>
                            <option value="">-- Choisir d'abord une division --</option>
                        </select>
                    </div>
                    {{-- <div class="col-md-4">
                        <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                        <input type="text" id="creer_bur_code" class="form-control"
                               placeholder="BUR-SRV-01" required>
                    </div> --}}
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nom du Bureau <span class="text-danger">*</span></label>
                        <input type="text" id="creer_bur_nom" class="form-control"
                               placeholder="Ex: Bureau Inventaire" required>
                    </div>
                    {{-- <div class="col-md-6">
                        <label class="form-label fw-semibold">Localisation</label>
                        <input type="text" id="creer_bur_localisation" class="form-control"
                               placeholder="Ex: Salle 204, Batiment A">
                    </div> --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea id="creer_bur_description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Annuler
                </button>
                <button class="btn btn-primary" onclick="soumettreCreerBureau()">
                    <i class="bi bi-check-circle-fill me-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>
 
{{-- MODALE MODIFIER BUREAU --}}
<div class="modal fade" id="modalModifierBureau" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Modifier le Bureau
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="alertModifierBureau"></div>
                <input type="hidden" id="modifier_bur_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Service <span class="text-danger">*</span></label>
                        <select id="modifier_bur_service" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            @foreach($tousServices as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->code }} - {{ $srv->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-3">
                        <label class="form-label fw-semibold">Statut</label>
                        <select id="modifier_bur_is_active" class="form-select">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div> --}}
                    {{-- <div class="col-md-4">
                        <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                        <input type="text" id="modifier_bur_code" class="form-control" required>
                    </div> --}}
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" id="modifier_bur_nom" class="form-control" required>
                    </div>
                    {{-- <div class="col-md-5">
                        <label class="form-label fw-semibold">Localisation</label>
                        <input type="text" id="modifier_bur_localisation" class="form-control">
                    </div> --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea id="modifier_bur_description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Annuler
                </button>
                <button class="btn btn-warning" onclick="soumettreModifierBureau()">
                    <i class="bi bi-save-fill me-1"></i>Mettre a jour
                </button>
            </div>
        </div>
    </div>
</div>
 
@push('scripts')
<script>
const ROUTE_BUR   = '{{ url("/bureaux") }}';
const ROUTE_SPDIV = '{{ url("/services/par-division") }}';
const CSRF_BUR    = document.querySelector('meta[name="csrf-token"]').content;
 
async function chargerServicesDivision(divisionId, selectId) {
    const sel = document.getElementById(selectId);
    sel.innerHTML = '<option value="">Chargement...</option>';
    if (!divisionId) {
        sel.innerHTML = '<option value="">-- Choisir d\'abord une division --</option>';
        return;
    }
    const res  = await fetch(`${ROUTE_SPDIV}/${divisionId}`);
    const data = await res.json();
    sel.innerHTML = '<option value="">-- Choisir un service --</option>';
    data.forEach(s => {
        sel.innerHTML += `<option value="${s.id}">${s.code} - ${s.nom}</option>`;
    });
}
 
function chargerBureau(b) {
    document.getElementById('modifier_bur_id').value          = b.id;
    document.getElementById('modifier_bur_service').value     = b.id_service;
    document.getElementById('modifier_bur_code').value        = b.code;
    document.getElementById('modifier_bur_nom').value         = b.nom;
    document.getElementById('modifier_bur_localisation').value= b.localisation ?? '';
    document.getElementById('modifier_bur_description').value = b.description ?? '';
    document.getElementById('modifier_bur_is_active').value   = b.is_active ? '1' : '0';
    document.getElementById('alertModifierBureau').innerHTML   = '';
}
 
async function soumettreCreerBureau() {
    const btn = event.currentTarget; btn.disabled = true;
    const payload = {
        id_service:  document.getElementById('creer_bur_service').value,
        code:        document.getElementById('creer_bur_code').value.trim(),
        nom:         document.getElementById('creer_bur_nom').value.trim(),
        localisation:document.getElementById('creer_bur_localisation').value.trim(),
        description: document.getElementById('creer_bur_description').value.trim(),
    };
    const res  = await fetch(ROUTE_BUR, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_BUR,'Accept':'application/json'},
        body: JSON.stringify(payload)
    });
    const data = await res.json();
    const al = document.getElementById('alertCreerBureau');
    if (data.success) {
        al.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
        setTimeout(() => location.reload(), 1200);
    } else {
        const err = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message ?? 'Erreur');
        al.innerHTML = `<div class="alert alert-danger">${err}</div>`;
        btn.disabled = false;
    }
}
 
async function soumettreModifierBureau() {
    const id  = document.getElementById('modifier_bur_id').value;
    const btn = event.currentTarget; btn.disabled = true;
    const payload = {
        id_service:  document.getElementById('modifier_bur_service').value,
        code:        document.getElementById('modifier_bur_code').value.trim(),
        nom:         document.getElementById('modifier_bur_nom').value.trim(),
        localisation:document.getElementById('modifier_bur_localisation').value.trim(),
        description: document.getElementById('modifier_bur_description').value.trim(),
        is_active:   document.getElementById('modifier_bur_is_active').value === '1',
        _method:     'PUT',
    };
    const res  = await fetch(`${ROUTE_BUR}/${id}`, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_BUR,'Accept':'application/json'},
        body: JSON.stringify(payload)
    });
    const data = await res.json();
    const al = document.getElementById('alertModifierBureau');
    if (data.success) {
        al.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
        setTimeout(() => location.reload(), 1200);
    } else {
        const err = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message ?? 'Erreur');
        al.innerHTML = `<div class="alert alert-danger">${err}</div>`;
        btn.disabled = false;
    }
}
 
async function supprimerBureau(id, nom) {
    if (!confirm(`Supprimer le bureau "${nom}" ?`)) return;
    const res  = await fetch(`${ROUTE_BUR}/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': CSRF_BUR, 'Accept':'application/json'}
    });
    const data = await res.json();
    const al = document.getElementById('alertGlobalBur');
    if (data.success) {
        al.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
        const row = document.getElementById(`row-bur-${id}`);
        if (row) row.remove();
    } else {
        al.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
    }
}
const TOUS_SERVICES = @json($tousServices->map(fn($s) => ['id' => $s->id, 'nom' => $s->nom, 'division_id' => $s->division_id]));

function chargerServicesDivision(divisionId, selectServiceId) {
    const selectService = document.getElementById(selectServiceId);
    selectService.innerHTML = '<option value="">-- Choisir un service --</option>';

    if (!divisionId) {
        selectService.innerHTML = '<option value="">-- Choisir d\'abord une division --</option>';
        return;
    }

    const filtres = TOUS_SERVICES.filter(s => String(s.division_id) === String(divisionId));

    if (filtres.length === 0) {
        selectService.innerHTML = '<option value="">Aucun service dans cette division</option>';
        return;
    }

    filtres.forEach(function(s) {
        const opt       = document.createElement('option');
        opt.value       = s.id;
        opt.textContent = s.nom;
        selectService.appendChild(opt);
    });
}
</script>
@endpush