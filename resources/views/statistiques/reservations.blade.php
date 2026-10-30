@extends('layouts.app')

@section('title','Stats Réservations')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<h4 class="fw-bold mb-3" style="color:#1F3864">
    <i class="bi bi-calendar-check me-2"></i>
    Statistiques des Réservations
</h4>

@include('statistiques._nav')

<div class="row g-3 mb-3">
    @foreach([
        ['Total demandes',$total,'calendar-event','1F3864'],
        ['Validées',$validees,'check-circle','198754'],
        ['Rejetées',$rejetees,'x-circle','dc3545'],
        ['En attente',$attente,'clock','ffc107'],
        ['Conflits IA',$conflits,'robot','6f42c1']
    ] as [$lbl,$val,$ico,$col])

    <div class="col">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body">
                <i class="bi bi-{{ $ico }} fs-2" style="color:#{{ $col }}"></i>
                <div class="fs-2 fw-bold mt-1">{{ $val }}</div>
                <div class="text-muted small">{{ $lbl }}</div>
            </div>
        </div>
    </div>

    @endforeach
</div>

<div class="row g-3">

    <!-- Graph -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                Répartition par statut
            </div>
            <div class="card-body">
                <canvas id="chartStatuts" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Indicateurs -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                Indicateurs d'efficacité
            </div>
            <div class="card-body">

                <!-- Validation -->
                <div class="d-flex justify-content-between mb-2">
                    <span>Taux de validation</span>
                    <strong>{{ $total>0 ? round($validees/$total*100) : 0 }}%</strong>
                </div>
                <div class="progress mb-3" style="height:10px">
                    <div class="progress-bar bg-success"
                         style="width:{{ $total>0 ? round($validees/$total*100) : 0 }}%">
                    </div>
                </div>

                <!-- Rejet -->
                <div class="d-flex justify-content-between mb-2">
                    <span>Taux de rejet</span>
                    <strong class="text-danger">
                        {{ $total>0 ? round($rejetees/$total*100) : 0 }}%
                    </strong>
                </div>
                <div class="progress mb-3" style="height:10px">
                    <div class="progress-bar bg-danger"
                         style="width:{{ $total>0 ? round($rejetees/$total*100) : 0 }}%">
                    </div>
                </div>

                <!-- Temps -->
                <div class="d-flex justify-content-between">
                    <span>Temps moyen de validation</span>
                    <strong>
                        {{ $tempsVal ? round($tempsVal,1).'h' : 'N/A' }}
                    </strong>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
new Chart(document.getElementById('chartStatuts'), {
    type: 'pie',
    data: {
        labels: ['En attente','Validées','Rejetées'],
        datasets: [{
            data: [{{ $attente }}, {{ $validees }}, {{ $rejetees }}],
            backgroundColor: ['#ffc107','#198754','#dc3545']
        }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
@endpush