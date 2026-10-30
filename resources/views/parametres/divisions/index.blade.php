<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-building-fill text-primary me-2"></i>Gestion des Divisions
        </h4>
    </div>
    <button type="button" class="btn btn-primary"
            data-bs-toggle="modal" data-bs-target="#modalCreerDivision">
        <i class="bi bi-plus-circle-fill me-2"></i>Nouvelle Division
    </button>
</div>
 
{{-- FILTRES --}}
<form method="GET" action="{{ route('parametres.index') }}" class="row g-2 mb-3">
    <input type="hidden" name="tab" value="divisions">
    <div class="col-md-3">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Rechercher code ou nom..."
               value="{{ request('search') }}">
    </div>
    <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-secondary btn-sm flex-fill">
            <i class="bi bi-search me-1"></i>Filtrer
        </button>
        <a href="{{ route('parametres.index', ['tab' => 'divisions']) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-x"></i>
        </a>
    </div>
</form>
 
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
<div id="alertGlobal"></div>
 
{{-- TABLEAU --}}
<div class="card shadow-sm border-0">
    <div style="max-height: calc(100vh - 420px); overflow-y: auto;">
        <table class="table table-sm table-hover table-striped align-middle mb-0">
            <thead class="table-dark" style="position:sticky;top:0;z-index:1;">
                <tr>
                    <th style="width:80px">Code</th>
                    <th>Nom de la Division</th>
                    <th class="text-center">Services</th>
                    <th class="text-center">Personnel</th>
                    <th class="text-center">Ressources</th>
                    <th class="text-end" style="width:130px">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($divisions as $division)
            <tr id="row-div-{{ $division->id }}">
                <td><span class="badge bg-primary fs-6">{{ $division->code }}</span></td>
                <td>
                    <div class="fw-semibold">{{ $division->nom }}</div>
                    @if($division->description)
                    <small class="text-muted text-truncate d-block" style="max-width:280px;">
                        {{ $division->description }}
                    </small>
                    @endif
                </td>
                <td class="text-center">
                    <span class="badge bg-info text-dark">{{ $division->services_count ?? 0 }}</span>
                </td>
                <td class="text-center">
                    <span class="badge bg-secondary">{{ $division->personnels_count ?? 0 }}</span>
                </td>
                <td class="text-center">
                    <span class="badge bg-warning text-dark">{{ $division->ressources_count ?? 0 }}</span>
                </td>
                {{-- <td class="text-center">
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox"
                               id="toggle-{{ $division->id }}"
                               {{ $division->is_active ? 'checked' : '' }}
                               onchange="toggleDivision({{ $division->id }}, this)">
                    </div>
                </td> --}}
                <td class="text-end">
                    <button type="button" class="btn btn-warning btn-sm py-0 px-2 me-1"
                            data-bs-toggle="modal" data-bs-target="#modalModifierDivision"
                            onclick="chargerDivision({{ json_encode($division) }})">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm py-0 px-2"
                            onclick="supprimerDivision({{ $division->id }}, '{{ addslashes($division->nom) }}')">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-5">
                    <i class="bi bi-building fs-1 d-block mb-3 opacity-25"></i>
                    Aucune division trouvee
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">
        {{ $divisions->appends(['tab' => 'divisions'])->links() }}
    </div>
</div>
 
{{-- MODALE CREER --}}
<div class="modal fade" id="modalCreerDivision" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white"
                 style="background:linear-gradient(135deg,#1F3864,#2E75B6);">
                <h5 class="modal-title">
                    <i class="bi bi-building-fill-add me-2"></i>Nouvelle Division
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="alertCreerDivision"></div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                        <input type="text" id="creer_div_code" class="form-control text-uppercase"
                               placeholder="Ex: DEP" maxlength="10" required>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-semibold">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" id="creer_div_nom" class="form-control"
                               placeholder="Ex: Division des Etudes et des Projets" required>
                    </div>
                    {{-- <div class="col-md-6">
                        <label class="form-label fw-semibold">Localisation</label>
                        <select id="creer_div_localisation" class="form-select">
                            <option value="">-- Choisir --</option>
                            <option value="Yaounde">Yaounde (Services Centraux)</option>
                            <option value="Douala">Douala (CID)</option>
                            <option value="Garoua">Garoua (CIG)</option>
                            <option value="Bafoussam">Bafoussam (CIB)</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div> --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea id="creer_div_description" class="form-control" rows="3"
                                  placeholder="Role et mission de la division..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Annuler
                </button>
                <button class="btn btn-primary" onclick="soumettreCreerDivision()">
                    <i class="bi bi-check-circle-fill me-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>
 
{{-- MODALE MODIFIER --}}
<div class="modal fade" id="modalModifierDivision" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Modifier la Division
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="alertModifierDivision"></div>
                <input type="hidden" id="modifier_div_id">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                        <input type="text" id="modifier_div_code" class="form-control text-uppercase"
                               maxlength="10" required>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-semibold">Nom complet <span class="text-danger">*</span></label>
                        <input type="text" id="modifier_div_nom" class="form-control" required>
                    </div>
                    {{-- <div class="col-md-6">
                        <label class="form-label fw-semibold">Localisation</label>
                        <select id="modifier_div_localisation" class="form-select">
                            <option value="">-- Choisir --</option>
                            <option value="Yaounde">Yaounde</option>
                            <option value="Douala">Douala</option>
                            <option value="Garoua">Garoua</option>
                            <option value="Bafoussam">Bafoussam</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div> --}}
                    {{-- <div class="col-md-3">
                        <label class="form-label fw-semibold">Statut</label>
                        <select id="modifier_div_is_active" class="form-select">
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div> --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea id="modifier_div_description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Annuler
                </button>
                <button class="btn btn-warning" onclick="soumettreModifierDivision()">
                    <i class="bi bi-save-fill me-1"></i>Mettre a jour
                </button>
            </div>
        </div>
    </div>
</div>
 
@push('scripts')
<script>
const ROUTE_DIVISIONS = '{{ url("/divisions") }}';
const CSRF_TOKEN      = document.querySelector('meta[name="csrf-token"]').content;
 
function chargerDivision(d) {
    document.getElementById('modifier_div_id').value          = d.id;
    document.getElementById('modifier_div_code').value        = d.code;
    document.getElementById('modifier_div_nom').value         = d.nom;
    document.getElementById('modifier_div_localisation').value= d.localisation ?? '';
    document.getElementById('modifier_div_description').value = d.description ?? '';
    document.getElementById('modifier_div_is_active').value   = d.is_active ? '1' : '0';
    document.getElementById('alertModifierDivision').innerHTML = '';
}
 
async function soumettreCreerDivision() {
    const btn = event.currentTarget; btn.disabled = true;
    const payload = {
        code:         document.getElementById('creer_div_code').value.toUpperCase().trim(),
        nom:          document.getElementById('creer_div_nom').value.trim(),
        localisation: document.getElementById('creer_div_localisation').value,
        description:  document.getElementById('creer_div_description').value.trim(),
    };
    try {
        const res  = await fetch(ROUTE_DIVISIONS, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        const al = document.getElementById('alertCreerDivision');
        if (data.success) {
            al.innerHTML = `<div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>${data.message}</div>`;
            setTimeout(() => location.reload(), 1200);
        } else {
            const err = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message ?? 'Erreur');
            al.innerHTML = `<div class="alert alert-danger">${err}</div>`;
            btn.disabled = false;
        }
    } catch(e) {
        document.getElementById('alertCreerDivision').innerHTML = `<div class="alert alert-danger">Erreur reseau : ${e.message}</div>`;
        btn.disabled = false;
    }
}
 
async function soumettreModifierDivision() {
    const id  = document.getElementById('modifier_div_id').value;
    const btn = event.currentTarget; btn.disabled = true;
    const payload = {
        code:         document.getElementById('modifier_div_code').value.toUpperCase().trim(),
        nom:          document.getElementById('modifier_div_nom').value.trim(),
        localisation: document.getElementById('modifier_div_localisation').value,
        description:  document.getElementById('modifier_div_description').value.trim(),
        is_active:    document.getElementById('modifier_div_is_active').value === '1',
        _method:      'PUT',
    };
    try {
        const res  = await fetch(`${ROUTE_DIVISIONS}/${id}`, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        const al = document.getElementById('alertModifierDivision');
        if (data.success) {
            al.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            setTimeout(() => location.reload(), 1200);
        } else {
            const err = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message ?? 'Erreur');
            al.innerHTML = `<div class="alert alert-danger">${err}</div>`;
            btn.disabled = false;
        }
    } catch(e) {
        document.getElementById('alertModifierDivision').innerHTML = `<div class="alert alert-danger">Erreur : ${e.message}</div>`;
        btn.disabled = false;
    }
}
 
async function supprimerDivision(id, nom) {
    if (!confirm(`Supprimer la division "${nom}" ?`)) return;
    const res  = await fetch(`${ROUTE_DIVISIONS}/${id}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept':'application/json'}
    });
    const data = await res.json();
    const al = document.getElementById('alertGlobal');
    if (data.success) {
        al.innerHTML = `<div class="alert alert-success alert-dismissible fade show">${data.message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
        const row = document.getElementById(`row-div-${id}`);
        if (row) row.remove();
    } else {
        al.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
    }
}
 
async function toggleDivision(id, checkbox) {
    const res  = await fetch(`${ROUTE_DIVISIONS}/${id}/toggle-actif`, {
        method: 'PATCH',
        headers: {'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept':'application/json'}
    });
    const data = await res.json();
    if (!data.success) { checkbox.checked = !checkbox.checked; alert(data.message); }
}
</script>
@endpush