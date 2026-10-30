{{-- Vue partielle : incluse dans parametres/index.blade.php (onglet Personnel) --}}
{{-- NE PAS mettre @extends ni @section ici --}}

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="fw-bold mb-0" style="color:#1F3864"><i class="bi bi-people me-2"></i>Gestion du Personnel</h4>
  @can('creer personnel')
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreatePersonnel">
    <i class="bi bi-person-plus me-1"></i>Ajouter
  </button>
  @endcan
</div>

<!-- Filtres -->
<div class="card shadow-sm border-0 mb-3"><div class="card-body py-2">
  <form method="GET" action="{{ route('parametres.index') }}" class="row g-2">
    <input type="hidden" name="tab" value="utilisateurs">
    <div class="col-md-4">
      <input type="text" name="search" class="form-control form-control-sm"
             placeholder="🔍 Nom, prénom, matricule..." value="{{ request('search') }}">
    </div>
    <div class="col-md-4">
      <select name="division_id" class="form-select form-select-sm">
        <option value="">Toutes divisions</option>
        @foreach($toutsDivisions as $d)
          <option value="{{ $d->id }}" {{ request('division_id') == $d->id ? 'selected' : '' }}>
            {{ $d->code }} — {{ $d->nom }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i> Filtrer</button>
    </div>
    <div class="col-md-2">
      <a href="{{ route('parametres.index', ['tab' => 'utilisateurs']) }}"
         class="btn btn-sm btn-outline-secondary w-100">Reset</a>
    </div>
  </form>
</div></div>

<!-- Liste -->
<div class="card shadow-sm border-0"><div class="card-body p-0">
  <div class="table-scroll">
    <table class="table table-hover table-sm mb-0">
      <thead>
        <tr>
          <th>Photo</th><th>Nom / Prénom</th><th>Matricule</th>
          <th>Division</th><th>Service</th><th>Bureau</th>
          <th>Rôle</th><th>Statut</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($personnels as $per)
        <tr>
          <td>
            @if($per->photo)
              <img src="{{ Storage::url($per->photo) }}" class="rounded-circle" width="36" height="36">
            @else
              <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center"
                   style="width:36px;height:36px">
                <span class="text-white fw-bold small">{{ $per->initiales }}</span>
              </div>
            @endif
          </td>
          <td>
            <strong>{{ $per->prenom }} {{ $per->nom }}</strong><br>
            <small class="text-muted">{{ $per->user?->email }}</small>
          </td>
          <td><code>{{ $per->matricule }}</code></td>
          <td><span class="badge bg-secondary">{{ $per->division?->code ?? '—' }}</span></td>
          <td><small>{{ Str::limit($per->service?->nom ?? '—', 20) }}</small></td>
          <td><small>{{ $per->bureau?->nom ?? '—' }}</small></td>
          <td><span class="badge bg-info text-dark">{{ $per->user?->getRoleNames()->first() ?? '—' }}</span></td>
          <td>
            @if($per->statut)
              <span class="badge bg-success">Actif</span>
            @else
              <span class="badge bg-danger">Inactif</span>
            @endif
          </td>
          <td>
            <div class="btn-group btn-group-sm">
              <a href="{{ route('personnels.show', $per) }}" class="btn btn-outline-info">
                <i class="bi bi-eye"></i>
              </a>
              @can('modifier personnel')
              <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEditPersonnel"
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
                        "role"        => $per->user?->getRoleNames()->first()
                      ]) }}'>
                <i class="bi bi-pencil"></i>
              </button>
              <form method="POST" action="{{ route('personnels.toggle', $per) }}" class="d-inline">
                @csrf @method('PATCH')
              </form>
              @endcan
              @can('supprimer personnel')
              <form method="POST" action="{{ route('personnels.destroy', $per) }}" class="d-inline"
                    onsubmit="return confirm('Supprimer ce personnel ?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
              @endcan
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center py-4 text-muted">Aucun personnel trouvé</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-3 py-2 border-top">
    {{ $personnels->appends(array_merge(request()->except('page'), ['tab' => 'utilisateurs']))->links() }}
  </div>
</div></div>

<!-- MODAL CRÉER PERSONNEL -->
@can('creer personnel')
<div class="modal fade" id="modalCreatePersonnel" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form method="POST" action="{{ route('personnels.store') }}">@csrf
      <div class="modal-header" style="background:#1F3864">
        <h5 class="modal-title text-white"><i class="bi bi-person-plus me-2"></i>Créer un compte personnel</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-semibold">Nom *</label>
          <input type="text" name="nom" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Prénom *</label>
          <input type="text" name="prenom" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Email *</label>
          <input type="email" name="email" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Matricule *</label>
          <input type="text" name="matricule" class="form-control" required placeholder="24GLO56IU"></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Mot de passe *</label>
          <input type="password" name="password" class="form-control" required minlength="8"></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Rôle *</label>
          <select name="role" class="form-select" required>
            <option value="personnel">Personnel</option>
            <option value="chef_division">Chef de Division</option>
            <option value="administrateur">Administrateur</option>
          </select></div>
        <div class="col-md-4"><label class="form-label">Division</label>
          <select name="division_id" class="form-select">
            <option value="">—</option>
            @foreach($toutsDivisions as $d)
              <option value="{{ $d->id }}">{{ $d->code }} — {{ $d->nom }}</option>
            @endforeach
          </select></div>
        <div class="col-md-4"><label class="form-label">Service</label>
          <select name="service_id" class="form-select">
            <option value="">—</option>
            @foreach($tousServices as $s)
              <option value="{{ $s->id }}">{{ $s->nom }}</option>
            @endforeach
          </select></div>
        <div class="col-md-4"><label class="form-label">Bureau</label>
          <select name="bureau_id" class="form-select">
            <option value="">—</option>
            @foreach($tousBureaux as $b)
              <option value="{{ $b->id }}">{{ $b->nom }}</option>
            @endforeach
          </select></div>
        <div class="col-md-6"><label class="form-label">Nom complet (navbar)</label>
          <input type="text" name="name" class="form-control"></div>
        {{-- <div class="col-md-6"><label class="form-label">Téléphone</label>
          <input type="text" name="telephone" class="form-control" placeholder="6XXXXXXXX"></div>
      </div></div> --}}
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary"><i class="bi bi-person-check me-1"></i>Créer</button>
      </div>
    </form>
  </div></div>
</div>
@endcan

<!-- MODAL ÉDITER PERSONNEL -->
@can('modifier personnel')
<div class="modal fade" id="modalEditPersonnel" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <form method="POST" action="" id="formEditPersonnel">@csrf @method('PUT')
      <div class="modal-header" style="background:#1F3864">
        <h5 class="modal-title text-white"><i class="bi bi-pencil me-2"></i>Modifier le personnel</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-semibold">Nom *</label>
          <input type="text" name="nom" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Prénom *</label>
          <input type="text" name="prenom" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Email *</label>
          <input type="email" name="email" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Matricule *</label>
          <input type="text" name="matricule" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Rôle *</label>
          <select name="role" class="form-select" required>
            <option value="personnel">Personnel</option>
            <option value="chef_division">Chef de Division</option>
            <option value="administrateur">Administrateur</option>
          </select></div>
        <div class="col-md-6"><label class="form-label">Téléphone</label>
          <input type="text" name="telephone" class="form-control" placeholder="6XXXXXXXX"></div>
        <div class="col-md-4"><label class="form-label">Division</label>
          <select name="division_id" class="form-select">
            <option value="">—</option>
            @foreach($toutsDivisions as $d)
              <option value="{{ $d->id }}">{{ $d->code }} — {{ $d->nom }}</option>
            @endforeach
          </select></div>
        <div class="col-md-4"><label class="form-label">Service</label>
          <select name="service_id" class="form-select">
            <option value="">—</option>
            @foreach($tousServices as $s)
              <option value="{{ $s->id }}">{{ $s->nom }}</option>
            @endforeach
          </select></div>
        <div class="col-md-4"><label class="form-label">Bureau</label>
          <select name="bureau_id" class="form-select">
            <option value="">—</option>
            @foreach($tousBureaux as $b)
              <option value="{{ $b->id }}">{{ $b->nom }}</option>
            @endforeach
          </select></div>
      </div></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Enregistrer</button>
      </div>
    </form>
  </div></div>
</div>
@endcan

@push('scripts')
<script>
document.querySelectorAll('.btn-outline-warning[data-per]').forEach(b => {
  b.addEventListener('click', () => {
    const d = JSON.parse(b.dataset.per);
    const form = document.getElementById('formEditPersonnel');
    form.action = '/personnels/' + d.id;
    ['nom','prenom','telephone','email','matricule'].forEach(k => {
      const el = form.querySelector('[name=' + k + ']');
      if (el) el.value = d[k] ?? '';
    });
    const roleEl = form.querySelector('[name=role]');
    if (roleEl && d.role) roleEl.value = d.role;
    ['division_id','service_id','bureau_id'].forEach(k => {
      const el = form.querySelector('[name=' + k + ']');
      if (el) el.value = d[k] ?? '';
    });
  });
});
</script>
@endpush