<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Classe fille Ressources matérielles (héritage depuis Ressources)
class RessourceMaterielle extends Model
{
    use HasFactory;

    protected $table = 'ressource_materielles';

    protected $fillable = [
        'ressource_id', 'marque', 'modele',
        'date_acquisition', 'capacite', 'valeur_achat',
    ];

    protected function casts(): array
    {
        return [
            'date_acquisition' => 'date',
            'valeur_achat'     => 'decimal:2',
        ];
    }

    // Lien vers la ressource parente (héritage 1:1)
    public function ressource()
    {
        return $this->belongsTo(Ressource::class);
    }

    // Valeur formatée en FCFA
    public function getValeurFormatteeAttribute(): string
    {
        return number_format($this->valeur_achat ?? 0, 0, ',', ' ') . ' FCFA';
    }
}

