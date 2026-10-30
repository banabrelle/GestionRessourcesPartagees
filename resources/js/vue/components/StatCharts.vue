<template>
  <div class="row g-3">
    <!-- Graphique occupation ressources -->
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-semibold">
          <i class="bi bi-pie-chart me-2" style="color:#1F3864"></i>
          Occupation des ressources
        </div>
        <div class="card-body">
          <canvas ref="chartOccupation" height="250"></canvas>
        </div>
      </div>
    </div>
    <!-- Graphique réservations par mois -->
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-semibold">
          <i class="bi bi-bar-chart me-2" style="color:#1F3864"></i>
          Réservations par type de ressource
        </div>
        <div class="card-body">
          <canvas ref="chartTypes" height="250"></canvas>
        </div>
      </div>
    </div>
    <!-- Évolution temporelle (ligne) -->
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <span class="fw-semibold">
            <i class="bi bi-graph-up me-2" style="color:#1F3864"></i>
            Évolution des réservations (12 mois)
          </span>
          <button @click="chargerDonneesTemporelles"
                  class="btn btn-sm btn-outline-primary">
            <i class="bi bi-arrow-clockwise me-1"></i>Actualiser
          </button>
        </div>
        <div class="card-body">
          <canvas ref="chartEvolution" height="120"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Chart, registerables } from 'chart.js';
import axios from 'axios';

Chart.register(...registerables);

const props = defineProps({
    statsData: { type: Object, default: () => ({}) }
});

const chartOccupation = ref(null);
const chartTypes      = ref(null);
const chartEvolution  = ref(null);
let   evolutionChart  = null;

onMounted(() => {
    renderOccupation();
    renderTypes();
    chargerDonneesTemporelles();
});

function renderOccupation() {
    new Chart(chartOccupation.value, {
        type: 'doughnut',
        data: {
            labels: ['Disponible', 'Occupé', 'En maintenance', 'En attente'],
            datasets: [{
                data: [
                    props.statsData.disponibles  ?? 0,
                    props.statsData.occupees     ?? 0,
                    props.statsData.maintenance  ?? 0,
                    props.statsData.en_attente   ?? 0,
                ],
                backgroundColor: ['#198754','#dc3545','#6c757d','#ffc107'],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.raw} ressource(s)`
                }}
            }
        }
    });
}

function renderTypes() {
    const par_type = props.statsData.par_type ?? {};
    new Chart(chartTypes.value, {
        type: 'bar',
        data: {
            labels: Object.keys(par_type).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
            datasets: [{
                label: 'Nombre de ressources',
                data: Object.values(par_type),
                backgroundColor: ['#1F3864','#2E75B6'],
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
}

async function chargerDonneesTemporelles() {
    try {
        const { data } = await axios.get('/api/statistiques/graphiques');
        const mois = ['Jan','Fév','Mar','Avr','Mai','Jun',
                       'Jul','Aoû','Sep','Oct','Nov','Déc'];
        const valeurs = mois.map((_, i) => data.par_mois?.[i + 1] ?? 0);

        if (evolutionChart) evolutionChart.destroy();
        evolutionChart = new Chart(chartEvolution.value, {
            type: 'line',
            data: {
                labels: mois,
                datasets: [{
                    label: 'Réservations',
                    data: valeurs,
                    borderColor: '#1F3864',
                    backgroundColor: 'rgba(31,56,100,0.12)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#1F3864',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    } catch (e) {
        console.warn('Erreur chargement données temporelles:', e);
    }
}
</script>

