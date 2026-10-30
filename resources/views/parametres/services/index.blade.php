<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-diagram-3-fill text-primary me-2"></i>Gestion des Services
        </h4>
    </div>
    <button type="button" class="btn btn-primary"
            data-bs-toggle="modal" data-bs-target="#modalCreerService">
        <i class="bi bi-plus-circle-fill me-2"></i>Nouveau Service
    </button>
</div>
 
<div id="alertGlobalSrv"></div>
 
{{-- FILTRES --}}
<form method="GET" action="{{ route('parametres.index') }}" class="row g-2 mb-3">
    <input type="hidden" name="tab" value="services">
    <div class="col-md-3">
        <select name="division" class="form-select form-select-sm">
            <option value="">Toutes les divisions</option>
            @foreach($toutsDivisions as $div)
            <option value="{{ $div->id }}" {{ request('division') == $div->id ? 'selected' : '' }}>
                {{ $div->code }} - {{ $div->nom }}
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
        <a href="{{ route('parametres.index', ['tab' => 'services']) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x"></i>
        </a>
    </div>
</form>
 
{{-- TABLEAU --}}
<div class="card shadow-sm border-0">
    <div style="max-height: calc(100vh - 400px); overflow-y: auto;">
        <table class="table table-sm table-hover table-striped align-middle mb-0">
            <thead class="table-dark" style="position:sticky;top:0;z-index:1;">
                <tr>
                    <th>Nom du Service</th>
                    <th>Division</th>
                    <th>Responsable</th>
                    <th class="text-center">Bureaux</th>
                    <th class="text-center">Personnel</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($services as $service)
            <tr id="row-srv-{{ $service->id }}">
                <td>
                    <div class="fw-semibold">{{ $service->nom }}</div>
                    @if($service->description)
                    <small class="text-muted">{{ Str::limit($service->description, 60) }}</small>
                    @endif
                </td>
                <td>
                    <span class="badge bg-secondary">
                        {{ $service->division?->code ?? '—' }}
                    </span>
                </td>
                <td>{{ $service->responsable ?? '—' }}</td>
                <td class="text-center">
                    <span class="badge bg-info text-dark">{{ $service->bureaux_count ?? 0 }}</span>
                </td>
                <td class="text-center">
                    <span class="badge bg-secondary">{{ $service->personnels_count ?? 0 }}</span>
                </td>
                <td class="text-end">
                    <button class="btn btn-warning btn-sm py-0 px-2 me-1"
                            data-bs-toggle="modal" data-bs-target="#modalModifierService"
                            onclick="chargerService({{ json_encode($service) }})">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button class="btn btn-danger btn-sm py-0 px-2"
                            onclick="supprimerService({{ $service->id }}, '{{ addslashes($service->nom) }}')">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-5">
                    <i class="bi bi-diagram-3 fs-1 d-block mb-3 opacity-25"></i>
                    Aucun service trouve
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $services->appends(['tab' => 'services'])->links() }}
    </div>
</div>
 
{{-- MODALE CREER SERVICE --}}
<div class="modal fade" id="modalCreerService" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white"
                 style="background:linear-gradient(135deg,#1F3864,#2E75B6);">
                <h5 class="modal-title">
                    <i class="bi bi-diagram-3-fill me-2"></i>Nouveau Service
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="alertCreerService"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Division <span class="text-danger">*</span></label>
                        <select id="creer_srv_division" class="form-select" required>
                            <option value="">-- Choisir une division --</option>
                            @foreach($toutsDivisions as $div)
                                <option value="{{ $div->id }}">{{ $div->code }} - {{ $div->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-3">
                        <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                        <input type="text" id="creer_srv_code" class="form-control"
                               placeholder="SRV-DEP-01" required>
                    </div> --}}
                    <div class="col-md-9">
                        <label class="form-label fw-semibold">Nom du Service <span class="text-danger">*</span></label>
                        <input type="text" id="creer_srv_nom" class="form-control"
                               placeholder="Ex: Service Reseau" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Responsable</label>
                        <input type="text" id="creer_srv_responsable" class="form-control"
                               placeholder="Nom du responsable">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea id="creer_srv_description" class="form-control" rows="3"
                                  placeholder="Description du service..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Annuler
                </button>
                <button class="btn btn-primary" onclick="soumettreCreerService()">
                    <i class="bi bi-check-circle-fill me-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>
 
{{-- MODALE MODIFIER SERVICE --}}
<div class="modal fade" id="modalModifierService" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Modifier le Service
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="alertModifierService"></div>
                <input type="hidden" id="modifier_srv_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Division <span class="text-danger">*</span></label>
                        <select id="modifier_srv_division" class="form-select" required>
                            <option value="">-- Choisir --</option>
                            @foreach($toutsDivisions as $div)
                            <option value="{{ $div->id }}"> {{ $div->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-3">
                        <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                        <input type="text" id="modifier_srv_code" class="form-control" required>
                    </div> --}}
                    <div class="col-md-9">
                        <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                        <input type="text" id="modifier_srv_nom" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Responsable</label>
                        <input type="text" id="modifier_srv_responsable" class="form-control">
                    </div> 
                    {{-- <div class="col-md-3">
                        <label class="form-label fw-semibold">Statut</label>
                        <select id="modifier_srv_is_active" class="form-select">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div> --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea id="modifier_srv_description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Annuler
                </button>
                <button class="btn btn-warning" onclick="soumettreModifierService()">
                    <i class="bi bi-save-fill me-1"></i>Mettre a jour
                </button>
            </div>
        </div>
    </div>
</div>
 
@push('scripts')
<script>
const ROUTE_SRV = '{{ url("/services") }}';
const CSRF_SRV  = document.querySelector('meta[name="csrf-token"]').content;
 
function chargerService(s) {
    document.getElementById('modifier_srv_id').value          = s.id;
    document.getElementById('modifier_srv_division').value    = s.id_division;
    document.getElementById('modifier_srv_code').value        = s.code;
    document.getElementById('modifier_srv_nom').value         = s.nom;
    document.getElementById('modifier_srv_responsable').value = s.responsable ?? '';
    document.getElementById('modifier_srv_description').value = s.description ?? '';
    document.getElementById('modifier_srv_is_active').value   = s.is_active ? '1' : '0';
    document.getElementById('alertModifierService').innerHTML  = '';
}
 
async function soumettreCreerService() {
    const btn = event.currentTarget; btn.disabled = true;
    const payload = {
        id_division:  document.getElementById('creer_srv_division').value,
        code:         document.getElementById('creer_srv_code').value.trim(),
        nom:          document.getElementById('creer_srv_nom').value.trim(),
        responsable:  document.getElementById('creer_srv_responsable').value.trim(),
        description:  document.getElementById('creer_srv_description').value.trim(),
    };
    const res  = await fetch(ROUTE_SRV, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_SRV,'Accept':'application/json'},
        body: JSON.stringify(payload)
    });
    const data = await res.json();
    const al = document.getElementById('alertCreerService');
    if (data.success) {
        al.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
        setTimeout(() => location.reload(), 1200);
    } else {
        const err = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message ?? 'Erreur');
        al.innerHTML = `<div class="alert alert-danger">${err}</div>`;
        btn.disabled = false;
    }
}
 
async function soumettreModifierService() {
    const id  = document.getElementById('modifier_srv_id').value;
    const btn = event.currentTarget; btn.disabled = true;
    const payload = {
        id_division:  document.getElementById('modifier_srv_division').value,
        code:         document.getElementById('modifier_srv_code').value.trim(),
        nom:          document.getElementById('modifier_srv_nom').value.trim(),
        responsable:  document.getElementById('modifier_srv_responsable').value.trim(),
        description:  document.getElementById('modifier_srv_description').value.trim(),
        is_active:    document.getElementById('modifier_srv_is_active').value === '1',
        _method:      'PUT',
    };
    const res  = await fetch(`${ROUTE_SRV}/${id}`, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_SRV,'Accept':'application/json'},
        body: JSON.stringify(payload)
    });
    const data = await res.json();
    const al = document.getElementById('alertModifierService');
    if (data.success) {
        al.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
        setTimeout(() => location.reload(), 1200);
    } else {
        const err = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message ?? 'Erreur');
        al.innerHTML = `<div class="alert alert-danger">${err}</div>`;
        btn.disabled = false;
    }
}
 
async function supprimerService(id, nom) {
    if (!confirm(`Supprimer le service "${nom}" ?`)) return;
    const res  = await fetch(`${ROUTE_SRV}/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': CSRF_SRV, 'Accept':'application/json'}
    });
    const data = await res.json();
    const al = document.getElementById('alertGlobalSrv');
    if (data.success) {
        al.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
        const row = document.getElementById(`row-srv-${id}`);
        if (row) row.remove();
    } else {
        al.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
    }
}
</script>
@endpush
 