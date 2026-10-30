<template>
  <div v-if="visible" class="alert alert-warning border-warning shadow-sm">
    <div class="d-flex align-items-center mb-3">
      <i class="bi bi-robot fs-4 me-3 text-warning"></i>
      <div>
        <h6 class="fw-bold mb-0">Conflit détecté par le Module IA</h6>
        <small class="text-muted">Cette ressource est déjà réservée sur ce créneau</small>
      </div>
      <button @click="visible = false" class="btn-close ms-auto"></button>
    </div>
    <!-- Créneaux alternatifs -->
    <div v-if="suggestions.creneaux?.length > 0" class="mb-3">
      <p class="fw-semibold small mb-2">
        <i class="bi bi-clock me-1"></i>Créneaux disponibles :
      </p>
      <div class="d-flex flex-wrap gap-2">
        <button v-for="c in suggestions.creneaux" :key="c.debut"
                @click="appliquerCreneau(c)"
                class="btn btn-sm btn-outline-success">
          <i class="bi bi-calendar-check me-1"></i>
          {{ formatDate(c.debut) }} → {{ formatHeure(c.fin) }}
        </button>
      </div>
    </div>
    <!-- Ressources similaires -->
    <div v-if="suggestions.ressources?.length > 0">
      <p class="fw-semibold small mb-2">
        <i class="bi bi-box-seam me-1"></i>Ressources similaires disponibles :
      </p>
      <div class="d-flex flex-wrap gap-2">
        <a v-for="r in suggestions.ressources" :key="r.id"
           :href="'/ressources/' + r.id"
           class="btn btn-sm btn-outline-info">
          {{ r.nom }}
          <small v-if="r.localisation" class="ms-1 text-muted">({{ r.localisation }})</small>
        </a>
      </div>
    </div>
    <!-- Actions IA -->
    <div class="mt-3 d-flex gap-2">
      <button v-if="suggestionId" @click="accepterSuggestion"
              class="btn btn-sm btn-success">
        <i class="bi bi-check me-1"></i>Accepter la suggestion IA
      </button>
      <button v-if="suggestionId" @click="ignorerSuggestion"
              class="btn btn-sm btn-outline-secondary">
        Ignorer
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    suggestions: { type: Object, default: () => ({}) },
    suggestionId: { type: Number, default: null },
});

const visible = ref(true);

function appliquerCreneau(c) {
    const debut = document.querySelector('[name=date_debut]');
    const fin   = document.querySelector('[name=date_fin]');
    if (debut) debut.value = c.debut;
    if (fin)   fin.value   = c.fin;
    visible.value = false;
}

async function accepterSuggestion() {
    if (!props.suggestionId) return;
    await axios.patch(`/api/ia/suggestions/${props.suggestionId}/accepter`);
    visible.value = false;
}

async function ignorerSuggestion() {
    if (!props.suggestionId) return;
    await axios.patch(`/api/ia/suggestions/${props.suggestionId}/ignorer`);
    visible.value = false;
}

function formatDate(dt) {
    return new Date(dt).toLocaleDateString('fr-FR',
        {day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'});
}
function formatHeure(dt) {
    return new Date(dt).toLocaleTimeString('fr-FR',
        {hour:'2-digit',minute:'2-digit'});
}
</script>

 
