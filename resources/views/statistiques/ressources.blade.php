@extends('layouts.app')

@section('title','Stats Ressources')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<h4 class="fw-bold mb-3" style="color:#1F3864">
  <i class="bi bi-box-seam me-2"></i>Statistiques des Ressources
</h4>

@include('statistiques._nav')

<!-- Ressources les plus utilisées -->
<div class="row g-3 mb-3">

  <div class="col-lg-7">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">
        Top 10 ressources les plus utilisées
      </div>
      <div class="card-body">
        <canvas id="chartTopRessources" height="300"></canvas>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-white fw-semibold">
        Taux d'occupation (%)
      </div>

      <div style="max-height:320px;overflow-y:auto">
        <table class="table table-sm mb-0">
          <thead class="table-light sticky-top">
            <tr>
              <th>Ressource</th>
              <th>Réservations</th>
              <th>Taux</th>
            </tr>
          </thead>

          <tbody>
            @foreach($tauxOccupation as $r)
            <tr>
              <td>{{ Str::limit($r->nom,25) }}</td>

              <td>
                <span class="badge bg-primary">
                  {{ $r->nb_validees }}
                </span>
              </td>

              <td>
                @php
                  $taux = $r->nb_total > 0
                    ? round($r->nb_validees / $r->nb_total * 100)
                    : 0;
                @endphp

                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:6px">
                    <div class="progress-bar {{ $taux>70?'bg-danger':($taux>40?'bg-warning':'bg-success') }}"
                         style="width:{{ $taux }}%">
                    </div>
                  </div>
                  <small>{{ $taux }}%</small>
                </div>
              </td>

            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

    </div>
  </div>

</div>

<!-- Ressources dormantes -->
<div class="card shadow-sm border-0">
  <div class="card-header bg-white fw-semibold">
    <i class="bi bi-moon me-2 text-secondary"></i>Ressources dormantes
    <span class="badge bg-secondary ms-1">{{ $dormantes->count() }}</span>
    <small class="text-muted ms-2">(aucune réservation dans les 30 derniers jours)</small>
  </div>

  <div style="max-height:220px;overflow-y:auto">
    <table class="table table-sm mb-0">
      <thead class="table-light sticky-top">
        <tr>
          <th>Nom</th>
          <th>Type</th>
          <th>Division</th>
          <th>Statut</th>
        </tr>
      </thead>

      <tbody>
        @forelse($dormantes as $r)
        <tr>
          <td>
            <a href="{{ route('ressources.show',$r) }}">
              {{ $r->nom }}
            </a>
          </td>

          <td>
            <span class="badge {{ $r->type_ressource==='materiel'?'bg-primary':'bg-info' }}">
              {{ ucfirst($r->type_ressource) }}
            </span>
          </td>

          <td>{{ $r->division?->code ?? '—' }}</td>
          <td>{{ $r->localisation ?? '—' }}</td>

          <td>
            <span class="badge bg-{{ $r->badge_color }}">
              {{ ucfirst(str_replace('_',' ',$r->etat)) }}
            </span>
          </td>
        </tr>

        @empty
        <tr>
          <td colspan="5" class="text-center text-success py-3">
            <i class="bi bi-check-circle me-2"></i>
            Aucune ressource dormante !
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script>
new Chart(document.getElementById('chartTopRessources'), {
  type: 'bar',
  data: {
    labels: [
      {!! $plusUtilisees->pluck('nom')->map(fn($n)=>"'".Str::limit($n,20)."'")->implode(',') !!}
    ],
    datasets: [{
      label: 'Réservations validées',
      data: [{{ $plusUtilisees->pluck('nb_reservations')->implode(',') }}],
      backgroundColor: '#1F3864',
      borderRadius: 4
    }]
  },
  options: {
    indexAxis: 'y',
    plugins: {
      legend: { display: false }
    },
    scales: {
      x: {
        beginAtZero: true,
        ticks: { precision: 0 }
      }
    }
  }
});
</script>
@endpush

@endsection