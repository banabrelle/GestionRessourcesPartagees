@extends('layouts.app')

@section('title','Stats Module aide à la décision')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<h4 class="fw-bold mb-3" style="color:#1F3864">
    <i class="bi bi-robot me-2"></i>
    Statistiques du Module aide a la décision 
</h4>

@include('statistiques._nav')

<!-- KPIs IA -->
<div class="row g-3 mb-3">
    @foreach([
        ['Suggestions proposées',$total,'robot','6f42c1'],
        ['Suggestions acceptées',$acceptees,'check-circle','198754'],
        ['Suggestions ignorées',$ignorees,'dash-circle','6c757d'],
        ['Taux d\'acceptation',$taux.'%','graph-up','fd7e14'],
        ['Temps gagné',$tempsTotal.'min','hourglass','0dcaf0'],
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
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                Répartition des suggestions
            </div>
            <div class="card-body">
                <canvas id="chartIa" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Taux -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                Taux d'acceptation par type
            </div>
            <div class="card-body">

                @foreach($parType as $pt)
                    @php
                        $tauxPt = $pt->total > 0 ? round($pt->acceptees / $pt->total * 100) : 0;
                    @endphp

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold">
                                {{ ucfirst($pt->type_suggestion) }}
                            </span>
                            <span>
                                {{ $pt->acceptees }} / {{ $pt->total }} ({{ $tauxPt }}%)
                            </span>
                        </div>

                        <div class="progress" style="height:10px">
                            <div class="progress-bar bg-success"
                                 style="width:{{ $tauxPt }}%">
                            </div>
                        </div>
                    </div>
                @endforeach

                <hr>

                <!-- Global -->
                <div class="d-flex justify-content-between fw-bold">
                    <span>Taux global d'acceptation</span>
                    <span class="text-success">{{ $taux }}%</span>
                </div>

                <div class="progress mt-2" style="height:12px">
                    <div class="progress-bar bg-success"
                         style="width:{{ $taux }}%">
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('ai.index') }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-list-ul me-1"></i>
                        Journal complet des suggestions
                    </a>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
new Chart(document.getElementById('chartIa'), {
    type: 'doughnut',
    data: {
        labels: ['Acceptées','Ignorées','En attente'],
        datasets: [{
            data: [
                {{ $acceptees }},
                {{ $ignorees }},
                {{ $total - $acceptees - $ignorees }}
            ],
            backgroundColor: ['#198754','#6c757d','#ffc107']
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