<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Classe fille Ressources logicielles (héritage depuis Ressources)
class RessourceLogicielle extends Model
{
    use HasFactory;

    protected $table = 'ressource_logicielles';

    protected $fillable = [
        'ressource_id', 'categorie', 'type_licence',
        'nombre_licence', 'cles_utilisees', 'editeur', 'version',
        'cle_produit', 'date_debut_licence', 'date_fin_licence', 'alerte_envoyee',
    ];

    protected function casts(): array
    {
        return [
            'date_debut_licence' => 'date',
            'date_fin_licence'   => 'date',
            'alerte_envoyee'     => 'boolean',
        ];
    }

    // Lien vers la ressource parente (héritage 1:1)
    public function ressource()
    {
        return $this->belongsTo(Ressource::class);
    }

    // ── Accessors ────────────────────────────────────────────────
    // Clés restantes — contrainte UML {nombre_licence >= réservations actives}
    public function getClesRestantesAttribute(): int
    {
        return max(0, $this->nombre_licence - $this->cles_utilisees);
    }

    // Taux d'utilisation (%)
    public function getTauxUtilisationAttribute(): int
    {
        if (!$this->nombre_licence) return 0;
        return (int) round(($this->cles_utilisees / $this->nombre_licence) * 100);
    }

    // Expiration dans moins de 30 jours
    public function getBientotExpireAttribute(): bool
    {
        return $this->date_fin_licence
            && $this->date_fin_licence->isFuture()
            && $this->date_fin_licence->diffInDays(now()) <= 30;
    }

    // A des clés disponibles
    public function hasClesDisponibles(): bool
    {
        return $this->cles_restantes > 0;
    }
}
