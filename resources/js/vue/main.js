/**
 * GERES-CENADI — Point d'entrée Vue.js SÉPARÉ
 * Ce fichier monte les composants Vue.js dans les divs dédiés
 * Il n'interfère JAMAIS avec le Blade ou Bootstrap JS
 */
import { createApp } from 'vue';
import axios from 'axios';

// Configurer axios avec le token CSRF Laravel
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN']     =
    document.querySelector('meta[name="csrf-token"]')?.content;

// Importer les composants
import CalendarView   from './components/CalendarView.vue';
import StatCharts     from './components/StatCharts.vue';
import ResourceMonitor from './components/ResourceMonitor.vue';
import ConflitBanner  from './components/ConflitBanner.vue';

document.addEventListener('DOMContentLoaded', () => {

    // ── Calendrier des réservations ────────────────────────────
    const calEl = document.getElementById('vue-calendar');
    if (calEl) {
        createApp(CalendarView, {
            reservationsData: JSON.parse(calEl.dataset.reservations || '[]'),
        }).mount('#vue-calendar');
    }

    // ── Graphiques statistiques ────────────────────────────────
    const chartsEl = document.getElementById('vue-charts');
    if (chartsEl) {
        createApp(StatCharts, {
            statsData: JSON.parse(chartsEl.dataset.stats || '{}'),
        }).mount('#vue-charts');
    }

    // ── Moniteur ressources temps réel ─────────────────────────
    const monitorEl = document.getElementById('vue-monitor');
    if (monitorEl) {
        createApp(ResourceMonitor).mount('#vue-monitor');
    }

    // ── Bannière conflit IA ────────────────────────────────────
    const conflitEl = document.getElementById('vue-conflit');
    if (conflitEl) {
        createApp(ConflitBanner, {
            suggestions: JSON.parse(conflitEl.dataset.suggestions || '{}'),
        }).mount('#vue-conflit');
    }
});
