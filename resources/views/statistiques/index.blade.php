@extends('layouts.app')
@section('title','Statistiques')
@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush
@section('content')

<style>
  body{
    background-color: #E3F3FC;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold mb-0" style="color:#1F3864"><i class="bi bi-bar-chart-line me-2"></i>Statistiques GERES-CENADI</h4>
  @can('exporter rapports')
  <a href="{{ route('export.statistiques-pdf') }}" class="btn btn-outline-danger btn-sm">
    <i class="bi bi-filetype-pdf me-1"></i>Exporter PDF
  </a>
  @endcan
</div>
<!-- Navigation sous-pages stats -->
<div class="nav nav-pills mb-4 gap-2 flex-wrap">
  @foreach([
    'statistiques.index'       => ['Globales',   'bi-globe'],
    'statistiques.ressources'  => ['Ressources', 'bi-box-seam'],
    'statistiques.divisions'   => ['Divisions',  'bi-diagram-3'],
    'statistiques.reservations'=> ['Réservations','bi-calendar-check'],
    'statistiques.temporelles' => ['Temporelles','bi-clock-history'],
    'statistiques.utilisateurs'=> ['Utilisateurs','bi-people'],
    'statistiques.licences'    => ['Licences',   'bi-key'],
    'statistiques.ia'          => ['Module IA',  'bi-robot'],
  ] as $route => [$label,$icon])
  <a href="{{ route($route) }}" class="nav-link {{ request()->routeIs($route) ? 'active' : 'bg-white border' }}">
    <i class="{{ $icon }} me-1"></i>{{ $label }}
  </a>
  @endforeach
</div>
<!-- Cartes globales -->
<div class="row g-3 mb-4">
  @foreach([
    ['Total ressources',      $stats_globales['total_ressources'],   'box-seam-fill',     '#1F3864', '#e8f4fd'],
    ['Total réservations',    $stats_globales['total_reservations'], 'calendar-check-fill','#198754', '#d1fadf'],
    ['Taux utilisation',      $stats_globales['taux_global'].'%',    'graph-up',           '#fd7e14', '#fff3cd'],
    ['Conflits IA détectés',  $stats_globales['conflits'],           'exclamation-triangle','#dc3545','#ffe0e0'],
    ['Personnel actif',       $stats_globales['total_personnels'],   'people-fill',        '#0dcaf0', '#d1f2fb'],
    ['Suggestions IA',        $stats_ia['total'],                    'robot',               '#6f42c1', '#ede0ff'],
  ] as [$lbl,$val,$ico,$col,$bg])
  <div class="col-sm-6 col-xl-2">
    <div class="card border-0 shadow-sm h-100" style="border-top:4px solid {{ $col }}!important">
      <div class="card-body text-center">
        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:{{ $bg }}">
          <i class="bi bi-{{ $ico }} fs-4" style="color:{{ $col }}"></i>
        </div>
        <div class="fs-3 fw-bold">{{ $val }}</div>
        <div class="text-muted small">{{ $lbl }}</div>
      </div>
    </div>
  </div>
  @endforeach
</div>
<!-- Graphiques -->
<div class="row g-3">
  <div class="col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">Répartition des ressources</div>
      <div class="card-body"><canvas id="chartRessources" height="220"></canvas></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">Statuts des réservations</div>
      <div class="card-body"><canvas id="chartReservations" height="220"></canvas></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">Module d'aide à la décision</div>
      <div class="card-body">
        <div class="d-flex justify-content-between mb-2"><span>Suggestions proposées</span><strong>{{ $stats_ia['total'] }}</strong></div>
        <div class="d-flex justify-content-between mb-2"><span>Acceptées</span><strong class="text-success">{{ $stats_ia['acceptees'] }}</strong></div>
        <div class="d-flex justify-content-between mb-3"><span>Taux d'acceptation</span><strong>{{ $stats_ia['taux'] }}%</strong></div>
        <div class="progress" style="height:12px">
          <div class="progress-bar bg-success" style="width:{{ $stats_ia['taux'] }}%"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- Graphique Vue.js (composant StatCharts) -->
  <div class="col-12">
    <div id="vue-charts"
         data-stats='{{ json_encode(["disponibles"=>$stats_ressources["disponibles"],"occupees"=>$stats_ressources["occupees"],"maintenance"=>$stats_ressources["maintenance"],"par_type"=>$stats_ressources["par_type"]]) }}'></div>
  </div>
</div>
@push('scripts')
<script>
new Chart(document.getElementById('chartRessources'),{type:'doughnut',data:{
  labels:['Disponible','Occupé','Maintenance'],
  datasets:[{data:[{{ $stats_ressources['disponibles'] }},{{ $stats_ressources['occupees'] }},{{ $stats_ressources['maintenance'] }}],
    backgroundColor:['#198754','#dc3545','#6c757d']}]},
  options:{plugins:{legend:{position:'bottom'}}}
});
new Chart(document.getElementById('chartReservations'),{type:'bar',data:{
  labels:['En attente','Validées','Rejetées'],
  datasets:[{data:[{{ $stats_reservations['attente'] }},{{ $stats_reservations['validees'] }},{{ $stats_reservations['rejetees'] }}],
    backgroundColor:['#ffc107','#198754','#dc3545']}]},
  options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});
</script>
@vite('resources/js/vue/main.js')
@endpush
@endsection
