@extends('layouts.app')

@section('title','Stats Divisions')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<h4 class="fw-bold mb-3" style="color:#1F3864">
  <i class="bi bi-diagram-3 me-2"></i>Statistiques par Division
</h4>

@include('statistiques._nav')

<div class="row g-3">

  <div class="col-lg-5">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">
        Répartition des réservations
      </div>
      <div class="card-body">
        <canvas id="chartDivisions" height="280"></canvas>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">
        Statistiques par division
      </div>

      <div style="max-height:360px;overflow-y:auto">
        <table class="table table-sm mb-0">
          <thead class="table-light sticky-top">
            <tr>
              <th>Division</th>
              <th>Personnel</th>
              <th>Réservations</th>
              <th>Validées</th>
              <th>Taux valid.</th>
            </tr>
          </thead>

          <tbody>
            @foreach($statsDiv as $div)
            <tr>
              <td>
                <span class="badge bg-primary me-1">{{ $div->code }}</span>
                {{ Str::limit($div->nom,30) }}
              </td>

              <td>{{ $div->nb_personnels }}</td>
              <td>{{ $div->nb_reservations }}</td>

              <td>
                <span class="badge bg-success">
                  {{ $div->nb_validees }}
                </span>
              </td>

              <td>
                <div class="d-flex align-items-center gap-1">
                  <div class="progress flex-grow-1" style="height:6px">
                    <div class="progress-bar bg-success"
                         style="width:{{ $div->taux_validation }}%">
                    </div>
                  </div>
                  <small>{{ $div->taux_validation }}%</small>
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

@push('scripts')
<script>
new Chart(document.getElementById('chartDivisions'), {
  type: 'doughnut',
  data: {
    labels: [
      {!! $repartition->pluck('code')->map(fn($c)=>"'".$c."'")->implode(',') !!}
    ],
    datasets: [{
      data: [{{ $repartition->pluck('total')->implode(',') }}],
      backgroundColor: [
        '#1F3864','#2E75B6','#198754','#ffc107',
        '#dc3545','#6c757d','#0dcaf0','#fd7e14'
      ]
    }]
  },
  options: {
    plugins: {
      legend: {
        position: 'right'
      }
    }
  }
});
</script>
@endpush

@endsection