<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Correspond à la classe parente Ressources du diagramme UML
class Ressource extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ressources';

    protected $fillable = [
        'division_id', 'type_ressource', 'etat', 'mode_partage',
        'nom', 'description', 'numero_serie', 
    ];

    // ── Constantes (attributs UML: etat, type_ressources, mode_partage) ──
    // Etats — correspond à l'attribut 'etat: enum' du diagramme
    const ETAT_DISPONIBLE          = 'disponible';
    const ETAT_OCCUPE              = 'occupe';
    const ETAT_EN_ATTENTE          = 'en_attente_validation';
    const ETAT_MAINTENANCE         = 'en_maintenance';
    const ETAT_EXPIRE              = 'expire';

    // Types — correspond à type_ressources: enum
    const TYPE_MATERIEL  = 'materiel';
    const TYPE_LOGICIEL  = 'logiciel';

    // Modes — correspond à mode_partage: enum
    const MODE_EXCLUSIF  = 'exclusif';
    const MODE_PARTAGE   = 'partage';
    const MODE_RESERVE   = 'reserve';

    // ── Relations UML ────────────────────────────────────────────
    // Ressource appartient à 1 Division
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // Ressource est tagée par 0..* Tags (many-to-many UML)
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'ressource_tags');
    }

    // Ressource Concerne 0..* Reservations
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Lien vers fiche matérielle (héritage)
    public function ficheMaterielle()
    {
        return $this->hasOne(RessourceMaterielle::class, 'ressource_id');
    }

    // Lien vers fiche logicielle (héritage)
    public function ficheLogicielle()
    {
        return $this->hasOne(RessourceLogicielle::class, 'ressource_id');
    }

    // ── Scopes de filtrage par statut ─────────────────────────────
    // Utilisés par ResourceController::index() pour les onglets de filtres
    public function scopeDisponible($q)    { return $q->where('etat', self::ETAT_DISPONIBLE); }
    public function scopeOccupe($q)        { return $q->where('etat', self::ETAT_OCCUPE); }
    public function scopeEnAttente($q)     { return $q->where('etat', self::ETAT_EN_ATTENTE); }
    public function scopeEnMaintenance($q) { return $q->where('etat', self::ETAT_MAINTENANCE); }
    public function scopeExpire($q)        { return $q->where('etat', self::ETAT_EXPIRE); }

    // Scopes par type
    public function scopeMateriel($q)  { return $q->where('type_ressource', self::TYPE_MATERIEL); }
    public function scopeLogiciel($q)  { return $q->where('type_ressource', self::TYPE_LOGICIEL); }

    // ── Accessor couleur badge pour les vues ──────────────────────
    public function getBadgeColorAttribute(): string
    {
        return match($this->etat) {
            self::ETAT_DISPONIBLE => 'success',
            self::ETAT_OCCUPE     => 'danger',
            self::ETAT_EN_ATTENTE => 'warning',
            self::ETAT_MAINTENANCE=> 'secondary',
            self::ETAT_EXPIRE     => 'dark',
            default               => 'info',
        };
    }

    // ── Vérifier disponibilité pour une période ───────────────────
    // Implémente la contrainte UML {no overlap}
    public function estDisponiblePour(
        string $dateDebut,
        string $dateFin,
        ?int $exclureReservationId = null
    ): bool {
        if ($this->etat !== self::ETAT_DISPONIBLE) return false;

        $q = $this->reservations()
            ->whereIn('statut', ['en_attente', 'valide_chef', 'valide_admin'])
            ->where(function($q) use ($dateDebut, $dateFin) {
                $q->whereBetween('date_debut', [$dateDebut, $dateFin])
                  ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                  ->orWhere(function($q2) use ($dateDebut, $dateFin) {
                      $q2->where('date_debut', '<=', $dateDebut)
                          ->where('date_fin', '>=', $dateFin);
                  });
            });
        if ($exclureReservationId) {
            $q->where('id', '!=', $exclureReservationId);
        }
        return $q->count() === 0;
    }
}

