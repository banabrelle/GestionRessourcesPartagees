@extends('layouts.app')

@section('title','Stats Licences')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<h4 class="fw-bold mb-3" style="color:#1F3864">
    <i class="bi bi-key me-2"></i>
    Statistiques des Licences Logicielles
</h4>

@include('statistiques._nav')

@if($bientotExpirees->count() > 0)
<div class="alert alert-danger border-0 shadow-sm mb-3">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>{{ $bientotExpirees->count() }} licence(s)</strong>
    expirent dans moins de 30 jours !
    <a href="{{ route('licences.index') }}?expiration=30"
       class="btn btn-sm btn-danger ms-3">
        Voir
    </a>
</div>
@endif

<div class="row g-3">

    <!-- Graph -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                Utilisation des licences
            </div>
            <div class="card-body">
                <canvas id="chartLicences" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                Détail par logiciel
            </div>

            <div style="max-height:320px;overflow-y:auto">
                <table class="table table-sm mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Logiciel</th>
                            <th>Éditeur</th>
                            <th>Utilisées</th>
                            <th>Taux</th>
                            <th>Expiration</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($licences as $lic)
                        <tr class="{{ $lic['bientot'] ? 'table-warning' : '' }}">

                            <td>
                                <strong>{{ Str::limit($lic['nom'],22) }}</strong>
                            </td>

                            <td>
                                <small>{{ $lic['editeur'] }}</small>
                            </td>

                            <td>
                                {{ $lic['utilises'] }} / {{ $lic['total'] }}
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <div class="progress flex-grow-1" style="height:6px">
                                        <div class="progress-bar 
                                            {{ $lic['taux'] >= 90 ? 'bg-danger' : ($lic['taux'] >= 70 ? 'bg-warning' : 'bg-success') }}"
                                            style="width:{{ $lic['taux'] }}%">
                                        </div>
                                    </div>
                                    <small>{{ $lic['taux'] }}%</small>
                                </div>
                            </td>

                            <td>
                                @if($lic['expire'])
                                    <span class="{{ $lic['bientot'] ? 'text-danger fw-bold' : '' }}">
                                        {{ \Carbon\Carbon::parse($lic['expire'])->format('d/m/Y') }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
new Chart(document.getElementById('chartLicences'), {
    type: 'bar',
    data: {
        labels: [
            {!! $licences->map(fn($l) => "'" . Str::limit($l['nom'],15) . "'")->implode(',') !!}
        ],
        datasets: [
            {
                label: 'Utilisées',
                data: [
                    {!! $licences->pluck('utilises')->implode(',') !!}
                ],
                backgroundColor: '#dc3545'
            },
            {
                label: 'Disponibles',
                data: [
                    {!! $licences->map(fn($l) => $l['total'] - $l['utilises'])->implode(',') !!}
                ],
                backgroundColor: '#198754'
            }
        ]
    },
    options: {
        plugins: {
            legend: { position: 'bottom' }
        },
        scales: {
            x: { stacked: true },
            y: {
                stacked: true,
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});
</script>
@endpush