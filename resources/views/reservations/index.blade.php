@extends('layouts.app')
@section('title','Réservations')
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
  <h4 class="fw-bold mb-0" style="color:#1F3864"><i class="bi bi-calendar-check me-2"></i>Réservations</h4>
  @can('creer reservations')
  <a href="{{ route('ressources.index') }}" class="btn btn-success">
    <i class="bi bi-plus-lg me-1"></i>Nouvelle réservation
  </a>
  @endcan
</div>
<!-- Filtres -->
<div class="card shadow-sm border-0 mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2">
      <div class="col-md-3">
        <select name="statut" class="form-select form-select-sm">
          <option value="">Tous statuts</option>
          @foreach(['en_attente'=>'En attente','valide_chef'=>'Validé (chef)','valide_admin'=>'Validé (admin)','rejete_chef'=>'Rejeté (chef)','rejete_admin'=>'Rejeté (admin)','annule'=>'Annulé','termine'=>'Terminé'] as $v=>$l)
          <option value="{{ $v }}" {{ request('statut')===$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <select name="ressource_id" class="form-select form-select-sm">
          <option value="">Toutes ressources</option>
          @foreach($ressources as $r)<option value="{{ $r->id }}" {{ request('ressource_id')==$r->id?'selected':'' }}>{{ $r->nom }}</option>@endforeach
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i> Filtrer</button></div>
      <div class="col-md-1"><a href="{{ route('reservations.index') }}" class="btn btn-sm btn-outline-secondary w-100"><i class="bi bi-x"></i></a></div>
    </form>
  </div>
</div>
<!-- Liste -->
<div class="card shadow-sm border-0">
  <div class="card-body p-0">
    <div class="table-scroll">
      <table class="table table-hover table-sm mb-0">
        <thead><tr><th>Personnel</th><th>Division</th><th>Ressource</th><th>Début</th><th>Fin</th><th>Statut</th><th>Conflit IA</th><th>Actions</th></tr></thead>
        <tbody>
          @forelse($reservations as $r)
          <tr>
            {{-- <td class="text-muted small">{{ $r->id }}</td> --}}
            <td>{{ $r->personnel->prenom }} {{ $r->personnel->nom }}<br><small class="text-muted">{{ $r->personnel->matricule }}</small></td>
            <td><span class="badge bg-secondary">{{ $r->personnel->division?->code??'—' }}</span></td>
            <td>{{ Str::limit($r->ressource->nom,25) }}</td>
            <td><small>{{ $r->date_debut->format('d/m/Y H:i') }}</small></td>
            <td><small>{{ $r->date_fin->format('d/m/Y H:i') }}</small></td>
            <td><span class="badge bg-{{ $r->badge_color }}">{{ $r->statut_label }}</span></td>
            <td>
              @if($r->conflit_detecte)<span class="badge bg-warning text-dark"><i class="bi bi-robot me-1"></i>Conflit</span>@endif
            </td>
            <td>
              <div class="btn-group btn-group-sm">
                <a href="{{ route('reservations.show',$r) }}" class="btn btn-outline-info"><i class="bi bi-eye"></i></a>
                @if($r->statut==='en_attente')
                  @can('valider chef reservations')
                  <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalValiderChef"
                          data-id="{{ $r->id }}">Chef ✓</button>
                  @endcan
                  @if($r->personnel_id===auth()->user()->personnel?->id)
                  <form method="POST" action="{{ route('reservations.annuler',$r) }}" class="d-inline" onsubmit="return confirm('Annuler ?')">
                    @csrf @method('PATCH')<button class="btn btn-outline-secondary"><i class="bi bi-x"></i></button>
                  </form>
                  @endif
                @endif
                @if($r->statut==='valide_chef')
                  @can('valider admin reservations')
                  <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalValiderAdmin"
                          data-id="{{ $r->id }}">Admin ✓</button>
                  @endcan
                @endif
                @if($r->bonEmprunt)
                <a href="{{ route('bons.telecharger',$r->bonEmprunt) }}" class="btn btn-outline-dark" title="Télécharger bon"><i class="bi bi-file-pdf"></i></a>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center py-4 text-muted">Aucune réservation</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $reservations->links() }}</div>
  </div>
</div>

<!-- MODAL Valider Chef -->
@can('valider chef reservations')
<div class="modal fade" id="modalValiderChef" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST" id="formValiderChef">@csrf @method('PATCH')
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Validation Chef de Division</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Commentaire (optionnel)</label>
        <textarea name="commentaire" class="form-control" rows="3" placeholder="Remarques..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-info text-white"><i class="bi bi-check-lg me-1"></i>Valider</button>
      </div>
    </form>
  </div></div>
</div>
@endcan

<!-- MODAL Valider Admin -->
@can('valider admin reservations')
<div class="modal fade" id="modalValiderAdmin" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST" id="formValiderAdmin">@csrf @method('PATCH')
      <div class="modal-header" style="background:#198754">
        <h5 class="modal-title text-white"><i class="bi bi-check2-all me-2"></i>Validation Administrative Finale</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-success py-2"><i class="bi bi-info-circle me-2"></i>Un bon d'emprunt PDF sera généré automatiquement.</div>
        <label class="form-label">Commentaire admin (optionnel)</label>
        <textarea name="commentaire" class="form-control" rows="3" placeholder="Observations..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-success"><i class="bi bi-check-all me-1"></i>Valider & Générer bon</button>
      </div>
    </form>
  </div></div>
</div>
@endcan



@push('scripts')<script>
document.querySelectorAll('[data-bs-target="#modalValiderChef"]').forEach(b=>{
  b.addEventListener('click',()=>{ document.getElementById('formValiderChef').action='/reservations/'+b.dataset.id+'/valider-chef'; });
});
document.querySelectorAll('[data-bs-target="#modalValiderAdmin"]').forEach(b=>{
  b.addEventListener('click',()=>{ document.getElementById('formValiderAdmin').action='/reservations/'+b.dataset.id+'/valider-admin'; });
});
</script>@endpush
@endsection
