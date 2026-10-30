<template>
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <h6 class="mb-0 fw-bold" style="color:#1F3864">
        <i class="bi bi-activity me-2"></i>Moniteur Ressources
        <span v-if="chargement" class="spinner-border spinner-border-sm ms-2"></span>
      </h6>
      <div>
        <small class="text-muted me-2">Mis à jour : {{ derniereMAJ }}</small>
        <button @click="actualiser" class="btn btn-xs btn-outline-primary btn-sm">
          <i class="bi bi-arrow-clockwise"></i>
        </button>
      </div>
    </div>
    <div class="card-body p-2">
      <!-- Compteurs rapides -->
      <div class="row g-2 mb-3">
        <div class="col-3" v-for="stat in compteurs" :key="stat.label">
          <div class="text-center p-2 rounded" :style="{ background: stat.bg }">
            <div class="fw-bold fs-5" :style="{ color: stat.color }">{{ stat.valeur }}</div>
            <div class="small text-muted">{{ stat.label }}</div>
          </div>
        </div>
      </div>
      <!-- Liste ressources occupées -->
      <div v-if="ressourcesOccupees.length > 0">
        <p class="small fw-semibold text-muted mb-1">Ressources actuellement occupées :</p>
        <div v-for="r in ressourcesOccupees" :key="r.id"
             class="d-flex justify-content-between align-items-center border-bottom py-1">
          <div>
            <span class="fw-semibold small">{{ r.nom }}</span>
            <span class="badge bg-secondary ms-1" style="font-size:9px">{{ r.division }}</span>
          </div>
          <span class="badge bg-danger" style="font-size:9px">Occupé</span>
        </div>
      </div>
      <div v-else class="text-center text-muted small py-2">
        <i class="bi bi-check-circle text-success me-1"></i>
        Toutes les ressources sont disponibles
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const etats       = ref([]);
const chargement  = ref(false);
const derniereMAJ = ref('—');
let   intervalId  = null;

const compteurs = computed(() => [
    { label:'Disponibles',  valeur: etats.value.filter(r=>r.etat==='disponible').length,          color:'#198754', bg:'#d1fadf' },
    { label:'Occupées',     valeur: etats.value.filter(r=>r.etat==='occupe').length,               color:'#dc3545', bg:'#ffe0e0' },
    { label:'En attente',   valeur: etats.value.filter(r=>r.etat==='en_attente_validation').length, color:'#fd7e14', bg:'#fff3cd' },
    { label:'Maintenance',  valeur: etats.value.filter(r=>r.etat==='en_maintenance').length,       color:'#6c757d', bg:'#f0f0f0' },
]);

const ressourcesOccupees = computed(() =>
    etats.value.filter(r => r.etat === 'occupe')
);

async function actualiser() {
    chargement.value = true;
    try {
        const { data } = await axios.get('/api/ressources/etats');
        etats.value = data.ressources ?? [];
        derniereMAJ.value = new Date().toLocaleTimeString('fr-FR');
    } catch (e) {
        console.warn('Erreur moniteur:', e);
    } finally {
        chargement.value = false;
    }
}

onMounted(() => {
    actualiser();
    // Polling toutes les 30 secondes
    intervalId = setInterval(actualiser, 30000);
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});
</script>

