@extends('layouts.app')

@section('title','Stats Temporelles')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0" style="color:#1F3864">
        <i class="bi bi-clock-history me-2"></i>
        Statistiques Temporelles
    </h4>

    <form method="GET" class="d-flex gap-2">
        <select name="annee" class="form-select form-select-sm" style="width:auto">
            @for($y = date('Y'); $y >= date('Y')-3; $y--)
                <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endfor
        </select>
        <button class="btn btn-sm btn-primary">Filtrer</button>
    </form>
</div>

@include('statistiques._nav')

<div class="row g-3">

    <!-- Evolution mensuelle -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                Évolution mensuelle des réservations — {{ $annee }}
            </div>
            <div class="card-body">
                <canvas id="chartEvolution" height="130"></canvas>
            </div>
        </div>
    </div>

    <!-- Heures -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                Créneaux les plus demandés (heure)
            </div>
            <div class="card-body">
                <canvas id="chartHeures" height="220"></canvas>
            </div>
        </div>
    </div>

    <!-- Jours -->
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-semibold">
                Réservations par jour de la semaine
            </div>
            <div class="card-body">
                <canvas id="chartJours" height="220"></canvas>
            </div>
        </div>
    </div>

</div>


@endsection

@push('scripts')
<script>
const mois = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

const dataMois = {!! json_encode(
    collect(range(1,12))->map(fn($i) => $parMois[$i]?->total ?? 0)->values()
) !!};

new Chart(document.getElementById('chartEvolution'), {
    type: 'line',
    data: {
        labels: mois,
        datasets: [{
            label: 'Réservations',
            data: dataMois,
            borderColor: '#1F3864',
            backgroundColor: 'rgba(31,56,100,0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 4
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});

const heures = Array.from({length:10}, (_,i) => i + 8);

const dataHeures = {!! json_encode(
    collect(range(8,17))->map(fn($h) => $parHeure->firstWhere('heure',$h)?->total ?? 0)->values()
) !!};

new Chart(document.getElementById('chartHeures'), {
    type: 'bar',
    data: {
        labels: heures.map(h => h + 'h'),
        datasets: [{
            label: 'Réservations',
            data: dataHeures,
            backgroundColor: '#2E75B6',
            borderRadius: 4
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});

const jours = ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];

const dataJours = {!! json_encode(
    collect(range(1,7))->map(fn($j) => $parJour->firstWhere('jour',$j)?->total ?? 0)->values()
) !!};

new Chart(document.getElementById('chartJours'), {
    type: 'bar',
    data: {
        labels: jours,
        datasets: [{
            label: 'Réservations',
            data: dataJours,
            backgroundColor: dataJours.map((_,i) =>
                (i === 0 || i === 6) ? '#dc3545' : '#198754'
            ),
            borderRadius: 4
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});
</script>
@endpush