<?php
namespace App\Services;

use App\Models\{Ressource, Reservation, RessourcesLogicielle};
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ConflictDetectionService
{
    /**
     * Détecte un conflit pour une ressource sur une période donnée.
     * Implémente la contrainte UML {no overlap}.
     */
    public function detecter(
        Ressource $ressource,
        string    $dateDebut,
        string    $dateFin,
        ?int      $exclureReservationId = null
    ): bool {
        // Cas spécial: ressource logicielle à licences multiples
        if ($ressource->type_ressource === 'logiciel') {
            return $this->detecterConflitLogiciel(
                $ressource, $dateDebut, $dateFin, $exclureReservationId
            );
        }

        // Ressource matérielle: conflit si chevauchement
        return $this->chevauchementExiste(
            $ressource->id, $dateDebut, $dateFin, $exclureReservationId
        );
    }

    /**
     * Pour les logiciels: conflit seulement si toutes les licences sont prises.
     * Implémente la contrainte UML {nombre_licence >= réservations actives}.
     */
    private function detecterConflitLogiciel(
        Ressource $ressource,
        string    $dateDebut,
        string    $dateFin,
        ?int      $exclure
    ): bool {
        $fiche = $ressource->ficheLogicielle;
        if (!$fiche) return false;

        // Compter les réservations actives qui chevauchent cette période
        $reservationsActives = Reservation::where('ressource_id', $ressource->id)
            ->whereIn('statut', ['en_attente', 'valide_chef', 'valide_admin'])
            ->where(fn($q) => $this->queryChevauche($q, $dateDebut, $dateFin))
            ->when($exclure, fn($q) => $q->where('id', '!=', $exclure))
            ->count();

        // Conflit si le nombre de réservations >= nombre de licences
        return $reservationsActives >= $fiche->nombre_licence;
    }

    /**
     * Vérifie s'il existe un chevauchement de réservation.
     */
    private function chevauchementExiste(
        int     $ressourceId,
        string  $dateDebut,
        string  $dateFin,
        ?int    $exclure
    ): bool {
        return Reservation::where('ressource_id', $ressourceId)
            ->whereIn('statut', ['en_attente', 'valide_chef', 'valide_admin'])
            ->where(fn($q) => $this->queryChevauche($q, $dateDebut, $dateFin))
            ->when($exclure, fn($q) => $q->where('id', '!=', $exclure))
            ->exists();
    }

    /**
     * Clause SQL de chevauchement de créneaux.
     * A chevauche B si : A.debut < B.fin ET A.fin > B.debut
     */
    private function queryChevauche($query, string $debut, string $fin)
    {
        return $query->where('date_debut', '<', $fin)
                     ->where('date_fin',   '>',  $debut);
    }

    /**
     * Recherche les N prochains créneaux disponibles pour une ressource.
     * Moteur temporel du module IA.
     *
     * @param  Ressource  $ressource
     * @param  int        $dureeHeures   Durée souhaitée
     * @param  int        $n             Nombre de suggestions
     * @param  string     $debutRecherche  Depuis quand chercher
     * @return array      [{debut, fin, score}]
     */
    public function prochainCreneaux(
        Ressource $ressource,
        int       $dureeHeures  = 2,
        int       $n            = 3,
        string    $debutRecherche = 'now'
    ): array {
        // Récupérer toutes les réservations futures actives
        $reservsFutures = Reservation::where('ressource_id', $ressource->id)
            ->whereIn('statut', ['en_attente', 'valide_chef', 'valide_admin'])
            ->where('date_fin', '>', now())
            ->orderBy('date_debut')
            ->get();

        $creneaux   = [];
        $candidat   = Carbon::parse($debutRecherche)->addHour()->startOfHour();
        $iterations = 0;
        $score      = 1.0;

        while (count($creneaux) < $n && $iterations < 500) {
            $fin    = (clone $candidat)->addHours($dureeHeures);
            $heure  = (int) $candidat->format('H');
            $conflit = false;

            // Règle métier: heures ouvrables CENADI (8h-18h, lun-ven)
            if ($heure < 8 || ($heure + $dureeHeures) > 18 || $candidat->isWeekend()) {
                // Sauter au prochain jour ouvrable à 8h si besoin
                if ($candidat->isWeekend() || $heure >= 18) {
                    $candidat = $candidat->copy()->nextWeekday()->setHour(8)->setMinute(0);
                } else {
                    $candidat->addHour();
                }
                $iterations++;
                continue;
            }

            // Vérifier chevauchements
            foreach ($reservsFutures as $r) {
                if ($candidat->lt($r->date_fin) && $fin->gt($r->date_debut)) {
                    $conflit  = true;
                    $candidat = $r->date_fin->copy()->addMinutes(30)->startOfHour();
                    break;
                }
            }

            if (!$conflit) {
                $creneaux[] = [
                    'debut' => $candidat->format('Y-m-d H:i'),
                    'fin'   => $fin->format('Y-m-d H:i'),
                    'score' => round($score, 2),
                    'label' => $candidat->isoFormat('ddd D MMM [à] H[h]mm'),
                ];
                $score -= 0.1;
                $candidat->addHour();
            }

            $iterations++;
        }

        return $creneaux;
    }

    /**
     * Obtenir les statistiques de conflits pour le tableau de bord.
     */
    public function statistiquesConflits(): array
    {
        return [
            'total_conflits' => Reservation::where('conflit_detecte', true)->count(),
            'par_ressource'  => Reservation::where('conflit_detecte', true)
                ->selectRaw('ressource_id, COUNT(*) as nb')
                ->with('ressource:id,nom')
                ->groupBy('ressource_id')
                ->orderByDesc('nb')
                ->take(5)->get(),
        ];
    }
}

