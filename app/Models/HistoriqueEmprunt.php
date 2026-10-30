<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Correspond à la classe historique_Emprunt du diagramme UML
class HistoriqueEmprunt extends Model
{
    use HasFactory;

    protected $table = 'historique_emprunts';

    protected $fillable = [
        'reservation_id', 'numero_bon',
        'date_retour_prevue', 'date_retour_relle',
        'etat_retour', 'fichier_pdf', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_retour_prevue' => 'datetime',
            'date_retour_relle'  => 'datetime',
        ];
    }

    // concerne 1 Reservation (UML)
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // Générer un numéro de bon unique
    public static function genererNumeroBon(): string
    {
        $annee = date('Y');
        $dernier = self::whereYear('created_at', $annee)->max('id') ?? 0;
        return 'BON-' . $annee . '-' . str_pad($dernier + 1, 4, '0', STR_PAD_LEFT);
    }

    // Est en retard ?
    public function getEnRetardAttribute(): bool
    {
        return $this->date_retour_prevue
            && $this->date_retour_prevue->isPast()
            && $this->etat_retour === 'non_rendu';
    }
}
