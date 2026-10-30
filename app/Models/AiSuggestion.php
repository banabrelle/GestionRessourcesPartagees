<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Table de journalisation du module IA
// Permet de calculer: taux d'acceptation, temps gagné
class AiSuggestion extends Model
{
    use HasFactory;

    protected $table = 'ai_suggestions';

    protected $fillable = [
        'personnel_id', 'reservation_id', 'type_suggestion',
        'suggestion_data', 'acceptee', 'ignoree', 'temps_gagne_minutes',
    ];

    protected function casts(): array
    {
        return [
            'suggestion_data'       => 'array',
            'acceptee'              => 'boolean',
            'ignoree'               => 'boolean',
            'temps_gagne_minutes'   => 'integer',
        ];
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
