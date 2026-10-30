@extends('layouts.app')

@section('title','Licences Logicielles')

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
    <h4 class="fw-bold mb-0" style="color:#1F3864">
        <i class="bi bi-key me-2"></i>
        Gestion des Licences Logicielles
    </h4>

    @can('gerer licences')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
        <i class="bi bi-plus-lg me-1"></i>
        Nouvelle fiche licence
    </button>
    @endcan
</div>

<!-- Onglets -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap gap-2">

            <a href="{{ route('licences.index') }}"
               class="btn btn-sm {{ !request('expiration') ? 'btn-dark' : 'btn-outline-dark' }}">
                Toutes
                <span class="badge bg-secondary">{{ $compteurs['toutes'] }}</span>
            </a>

            <a href="{{ route('licences.index',['expiration'=>30]) }}"
               class="btn btn-sm {{ request('expiration')==30 ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                Expiration &lt;30j
                <span class="badge bg-secondary">{{ $compteurs['expir30'] }}</span>
            </a>

            <a href="{{ route('licences.index',['expiration'=>7]) }}"
               class="btn btn-sm {{ request('expiration')==7 ? 'btn-danger' : 'btn-outline-danger' }}">
                Expiration &lt;7j
                <span class="badge bg-secondary">{{ $compteurs['expir7'] }}</span>
            </a>

            <a href="{{ route('licences.index',['saturees'=>1]) }}"
               class="btn btn-sm btn-outline-dark">
                Saturées
                <span class="badge bg-secondary">{{ $compteurs['saturees'] }}</span>
            </a>

        </div>
    </div>
</div>

<!-- Recherche -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">

            @if(request('expiration'))
                <input type="hidden" name="expiration" value="{{ request('expiration') }}">
            @endif

            <div class="col-md-4">
                <input type="text" name="search"
                       class="form-control form-control-sm"
                       placeholder="🔍 Nom logiciel, éditeur..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <select name="type_licence" class="form-select form-select-sm">
                    <option value="">Tous types</option>
                    @foreach([
                        'proprietaire'=>'Propriétaire',
                        'libre'=>'Libre',
                        'freemium'=>'Freemium',
                        'abonnement'=>'Abonnement'
                    ] as $v=>$l)
                        <option value="{{ $v }}" {{ request('type_licence')===$v ? 'selected' : '' }}>
                            {{ $l }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('licences.index') }}"
                   class="btn btn-sm btn-outline-secondary w-100">
                    Reset
                </a>
            </div>

        </form>
    </div>
</div>

<!-- Liste -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">

        <div class="table-scroll">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th>Logiciel</th>
                        <th>Éditeur</th>
                        <th>Type</th>
                        <th>Licences</th>
                        <th>Utilisation</th>
                        <th>Expiration</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($licences as $lic)
                    <tr class="{{ $lic->bientot_expire ? 'table-warning' : '' }}">

                        <td>
                            <a href="{{ route('ressources.show',$lic->ressource) }}">
                                <strong>{{ $lic->ressource->nom }}</strong>
                            </a>
                        </td>

                        <td>
                            {{ $lic->editeur ?? '—' }}<br>
                            <small class="text-muted">v{{ $lic->version }}</small>
                        </td>

                        <td>
                            <span class="badge bg-secondary">
                                {{ ucfirst($lic->type_licence) }}
                            </span>
                        </td>

                        <td>
                            <span class="fw-bold">{{ $lic->cles_utilisees }}</span>
                            / {{ $lic->nombre_licence }}<br>

                            <small class="text-{{ $lic->cles_restantes>0 ? 'success' : 'danger' }}">
                                {{ $lic->cles_restantes }} restante(s)
                            </small>
                        </td>

                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <div class="progress flex-grow-1" style="height:8px">
                                    <div class="progress-bar
                                        {{ $lic->taux_utilisation>=90 ? 'bg-danger' : ($lic->taux_utilisation>=70 ? 'bg-warning' : 'bg-success') }}"
                                        style="width:{{ $lic->taux_utilisation }}%">
                                    </div>
                                </div>
                                <small>{{ $lic->taux_utilisation }}%</small>
                            </div>
                        </td>

                        <td>
                            @if($lic->date_fin_licence)
                                <span class="{{ $lic->bientot_expire ? 'text-danger fw-bold' : '' }}">
                                    {{ $lic->date_fin_licence->format('d/m/Y') }}
                                </span>

                                @if($lic->bientot_expire)
                                    <br>
                                    <small class="badge bg-danger">
                                        {{ $lic->date_fin_licence->diffForHumans() }}
                                    </small>
                                @endif
                            @else
                                <span class="text-muted">Illimitée</span>
                            @endif
                        </td>

                        <td>
                            <div class="btn-group btn-group-sm">

                                @can('gerer licences')

                                <!-- Allouer -->
                                <form method="POST"
                                      action="{{ route('licences.allouer',$lic) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-outline-success"
                                            onclick="return confirm('Allouer une clé à {{ $lic->ressource->nom }} ?')">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </form>

                                <!-- Libérer -->
                                <form method="POST"
                                      action="{{ route('licences.liberer',$lic) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-outline-warning">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                </form>

                                <!-- Edit -->
                                <button class="btn btn-outline-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit"
                                        {{-- data-lic="@json([
                                                "id" => $lic->id,
                                                "nombre" => $lic->nombre_licence,
                                                "utilisees" => $lic->cles_utilisees,
                                                "editeur" => $lic->editeur,
                                                "version" => $lic->version,
                                                "type" => $lic->type_licence,
                                                "fin" => optional($lic->date_fin_licence)->format("Y-m-d")
                                            ])" --}}
                                    <i class="bi bi-pencil"></i>
                                </button>

                                @endcan

                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Aucune fiche licence
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 py-2 border-top">
            {{ $licences->links() }}
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-lic]').forEach(b => {
    b.addEventListener('click', () => {
        const d = JSON.parse(b.dataset.lic);

        document.getElementById('formEditLic').action = '/licences/' + d.id;
        document.getElementById('editLicNb').value = d.nombre ?? 1;
        document.getElementById('editLicUtil').value = d.utilisees ?? 0;
        document.getElementById('editLicEdit').value = d.editeur ?? '';
        document.getElementById('editLicVer').value = d.version ?? '';
        document.getElementById('editLicType').value = d.type ?? 'proprietaire';
        document.getElementById('editLicFin').value = d.fin ?? '';
    });
});
</script>
@endpush