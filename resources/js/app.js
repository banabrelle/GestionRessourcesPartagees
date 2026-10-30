import './bootstrap';

import Alpine from 'alpinejs';
// Point d'entrée JavaScript principal — Blade uniquement
// NE PAS importer Vue.js ici pour garder la séparation

// Importations globales uniquement liées à Blade/Bootstrap
// import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import flatpickr from 'flatpickr';
import { French } from 'flatpickr/dist/l10n/fr.js';

// Flatpickr global pour les champs datetime dans les formulaires Blade
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[type="datetime-local"]').forEach(el => {
        flatpickr(el, {
            locale: French,
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
        });
    });

    // Remplissage automatique créneau IA suggéré
    document.querySelectorAll('.use-creneau').forEach(btn => {
        btn.addEventListener('click', () => {
            const debut = document.querySelector('[name=date_debut]');
            const fin   = document.querySelector('[name=date_fin]');
            if (debut) debut.value = btn.dataset.debut;
            if (fin)   fin.value   = btn.dataset.fin;
            btn.closest('.alert')?.remove();
        });
    });
});


window.Alpine = Alpine;

Alpine.start();
