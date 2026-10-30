<template>
  <div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-white">
      <h6 class="mb-0 fw-bold" style="color:#1F3864">
        <i class="bi bi-calendar3 me-2"></i>Calendrier des réservations
      </h6>
      <div class="btn-group btn-group-sm">
        <button class="btn btn-outline-secondary" @click="moisPrecedent">
          <i class="bi bi-chevron-left"></i>
        </button>
        <button class="btn btn-outline-secondary" disabled style="min-width:160px">
          {{ nomMoisCourant }} {{ anneeCourante }}
        </button>
        <button class="btn btn-outline-secondary" @click="moisSuivant">
          <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>
    <div class="card-body p-0">
      <!-- En-têtes jours -->
      <div class="row g-0 border-bottom">
        <div v-for="j in joursNoms" :key="j"
             class="col text-center py-1 small fw-bold text-muted">
          {{ j }}
        </div>
      </div>
      <!-- Grille calendrier -->
      <div v-for="(semaine, idx) in calendrier" :key="idx" class="row g-0">
        <div v-for="jour in semaine" :key="jour.date"
             class="col border-end border-bottom"
             style="min-height:80px; padding:4px; cursor:pointer;"
             :class="{
               'bg-light text-muted': !jour.moisCourant,
               'bg-primary bg-opacity-10': jour.estAujourdHui,
             }"
             @click="selectionnerJour(jour)">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-semibold small"
                  :class="jour.estAujourdHui ? 'badge bg-primary rounded-circle' : ''">
              {{ jour.num }}
            </span>
          </div>
          <!-- Réservations du jour -->
          <div v-for="res in jour.reservations.slice(0,2)" :key="res.id"
               class="badge w-100 text-start text-truncate mb-1"
               style="font-size:9px; white-space:nowrap; overflow:hidden;"
               :class="badgeClasse(res.statut)"
               :title="res.ressource + ' — ' + res.personnel">
            {{ res.ressource }}
          </div>
          <div v-if="jour.reservations.length > 2"
               class="badge bg-secondary w-100" style="font-size:9px">
            +{{ jour.reservations.length - 2 }} autres
          </div>
        </div>
      </div>
    </div>
    <!-- Légende -->
    <div class="card-footer bg-white d-flex gap-3 flex-wrap">
      <span class="badge bg-warning text-dark">En attente</span>
      <span class="badge bg-info text-white">Validé (chef)</span>
      <span class="badge bg-success">Validé (admin)</span>
      <span class="badge bg-danger">Rejeté</span>
      <span class="badge bg-secondary">Annulé</span>
    </div>
    <!-- Modal détail jour -->
    <div v-if="jourSelectionne" class="position-fixed top-0 start-0 w-100 h-100"
         style="background:rgba(0,0,0,.5); z-index:1055" @click.self="jourSelectionne=null">
      <div class="card" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:400px;max-height:80vh;overflow-y:auto">
        <div class="card-header d-flex justify-content-between" style="background:#1F3864">
          <h6 class="text-white mb-0">Réservations du {{ jourSelectionne.date }}</h6>
          <button class="btn-close btn-close-white" @click="jourSelectionne=null"></button>
        </div>
        <div class="card-body p-0">
          <div v-for="res in jourSelectionne.reservations" :key="res.id"
               class="border-bottom p-3">
            <div class="fw-semibold">{{ res.ressource }}</div>
            <div class="small text-muted">{{ res.personnel }}</div>
            <div class="d-flex justify-content-between mt-1">
              <small>{{ formatHeure(res.date_debut) }} → {{ formatHeure(res.date_fin) }}</small>
              <span class="badge" :class="badgeClasse(res.statut)">{{ res.statut }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    reservationsData: { type: Array, default: () => [] }
});

const dateCourante   = ref(new Date());
const jourSelectionne = ref(null);
const joursNoms      = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];

const nomMoisCourant = computed(() =>
    dateCourante.value.toLocaleString('fr-FR', { month: 'long' })
);
const anneeCourante = computed(() => dateCourante.value.getFullYear());

const calendrier = computed(() => {
    const annee = dateCourante.value.getFullYear();
    const mois  = dateCourante.value.getMonth();
    const aujourd = new Date();
    let debut = new Date(annee, mois, 1);
    // Ajuster pour commencer lundi (ISO)
    debut.setDate(debut.getDate() - ((debut.getDay() + 6) % 7));

    const semaines = [];
    let courant = new Date(debut);

    for (let s = 0; s < 6; s++) {
        const semaine = [];
        for (let j = 0; j < 7; j++) {
            const dateStr = courant.toISOString().split('T')[0];
            semaine.push({
                date:        dateStr,
                num:         courant.getDate(),
                moisCourant: courant.getMonth() === mois,
                estAujourdHui: courant.toDateString() === aujourd.toDateString(),
                reservations: props.reservationsData.filter(r =>
                    r.date_debut?.startsWith(dateStr)
                ),
            });
            courant.setDate(courant.getDate() + 1);
        }
        semaines.push(semaine);
        if (courant.getMonth() !== mois && s >= 4) break;
    }
    return semaines;
});

function moisPrecedent() {
    dateCourante.value = new Date(
        dateCourante.value.getFullYear(),
        dateCourante.value.getMonth() - 1, 1
    );
}
function moisSuivant() {
    dateCourante.value = new Date(
        dateCourante.value.getFullYear(),
        dateCourante.value.getMonth() + 1, 1
    );
}
function selectionnerJour(jour) {
    if (jour.reservations.length > 0) jourSelectionne.value = jour;
}
function badgeClasse(statut) {
    return {
        'en_attente':    'bg-warning text-dark',
        'valide_chef':   'bg-info text-white',
        'valide_admin':  'bg-success',
        'rejete_chef':   'bg-danger',
        'rejete_admin':  'bg-danger',
        'annule':        'bg-secondary',
        'termine':       'bg-dark',
    }[statut] ?? 'bg-light text-dark';
}
function formatHeure(dt) {
    return dt ? new Date(dt).toLocaleTimeString('fr-FR',{hour:'2-digit',minute:'2-digit'}) : '';
}
</script>

