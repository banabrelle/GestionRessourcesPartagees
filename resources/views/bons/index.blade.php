@extends('layouts.app')
@section('title','Bons d\'emprunt')
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
    <i class="bi bi-file-earmark-text me-2"></i>Bons d'Emprunt
  </h4>

  @can('exporter rapports')
  <a href="{{ route('export.reservations-pdf') }}" class="btn btn-outline-danger btn-sm">
    <i class="bi bi-filetype-pdf me-1"></i>Exporter tout en PDF
  </a>
  @endcan
</div>

<!-- Liste défilante -->
<div class="card shadow-sm border-0">
  <div class="card-body p-0">
    <div class="table-scroll">
      <table class="table table-hover table-sm mb-0">
        <thead>
          <tr>
            <th>N° Bon</th>
            <th>Personnel</th>
            <th>Division</th>
            <th>Ressource</th>
            <th>Début</th>
            <th>Fin prévue</th>
            <th>Retour réel</th>
            <th>État retour</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bons as $bon)
          @php $r = $bon->reservation; @endphp
          <tr class="{{ $bon->en_retard ? 'table-danger' : '' }}">
            <td><code class="fw-bold">{{ $bon->numero_bon }}</code></td>

            <td>
              {{ $r->personnel->prenom }} {{ $r->personnel->nom }}
              <br>
              <small class="text-muted">{{ $r->personnel->matricule }}</small>
            </td>

            <td>
              <span class="badge bg-secondary">
                {{ $r->personnel->division?->code ?? '—' }}
              </span>
            </td>

            <td>{{ Str::limit($r->ressource->nom,25) }}</td>

            <td>
              <small>{{ $r->date_debut->format('d/m/Y H:i') }}</small>
            </td>

            <td>
              <small>
                {{ $bon->date_retour_prevue?->format('d/m/Y H:i') ?? '—' }}
              </small>
            </td>

            <td>
              @if($bon->date_retour_relle)
                <small class="text-success">
                  {{ $bon->date_retour_relle->format('d/m/Y H:i') }}
                </small>
              @elseif($bon->en_retard)
                <span class="badge bg-danger">
                  <i class="bi bi-exclamation-triangle me-1"></i>En retard
                </span>
              @else
                <span class="badge bg-warning text-dark">
                  Pas encore rendu
                </span>
              @endif
            </td>

            <td>
              @php
                $etats = [
                  'non_rendu' => 'warning',
                  'rendu_bon_etat' => 'success',
                  'rendu_endommage' => 'danger',
                  'perdu' => 'dark'
                ];
              @endphp

              <span class="badge bg-{{ $etats[$bon->etat_retour] ?? 'secondary' }}">
                {{ ucfirst(str_replace('_',' ',$bon->etat_retour)) }}
              </span>
            </td>

            <td>
              <div class="btn-group btn-group-sm">
                <a href="{{ route('bons.telecharger',$bon) }}" class="btn btn-outline-dark" title="Télécharger PDF">
                  <i class="bi bi-file-pdf"></i>
                </a>

                <a href="{{ route('bons.apercu',$bon) }}" class="btn btn-outline-info" target="_blank" title="Aperçu">
                  <i class="bi bi-eye"></i>
                </a>

                @can('gerer ressources')
                @if($bon->etat_retour === 'non_rendu')
                <button class="btn btn-outline-success btn-enregistrer-retour"
                        data-bs-toggle="modal"
                        data-bs-target="#modalRetour"
                        data-id="{{ $bon->id }}"
                        data-nom="{{ $r->ressource->nom }}"
                        title="Enregistrer retour">
                  <i class="bi bi-box-arrow-in-left"></i>
                </button>
                @endif
                @endcan
              </div>
            </td>

          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center py-4 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2"></i>
              Aucun bon d'emprunt
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-3 py-2 border-top">
      {{ $bons->links() }}
    </div>

  </div>
</div>

<!-- ══ MODAL ENREGISTRER RETOUR ══ -->
@can('gerer ressources')
<div class="modal fade" id="modalRetour" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="POST" id="formRetour">
        @csrf
        @method('PATCH')

        <div class="modal-header" style="background:#1F3864">
          <h5 class="modal-title text-white">
            <i class="bi bi-box-arrow-in-left me-2"></i>Enregistrer le Retour
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="alert alert-info py-2 mb-3">
            <i class="bi bi-box-seam me-2"></i>
            Ressource : <strong id="retourNom"></strong>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">
              État de retour <span class="text-danger">*</span>
            </label>
            <select name="etat_retour" class="form-select" required>
              <option value="rendu_bon_etat">✅ Rendu en bon état</option>
              <option value="rendu_endommage">⚠ Rendu endommagé</option>
              <option value="perdu">❌ Perdu / Introuvable</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Observations</label>
            <textarea name="notes" class="form-control" rows="3"
                      placeholder="Remarques sur l'état de retour..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i>Enregistrer
          </button>
        </div>

      </form>

    </div>
  </div>
</div>
@endcan

@push('scripts')
<script>
document.querySelectorAll('.btn-enregistrer-retour').forEach(b => {
  b.addEventListener('click', () => {
    document.getElementById('formRetour').action = '/bons/' + b.dataset.id + '/retour';
    document.getElementById('retourNom').textContent = b.dataset.nom;
  });
});
</script>
@endpush

@endsection