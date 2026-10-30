<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Correspond à la classe Notification du diagramme UML
class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'type', 'titre',
        'message', 'etat', 'lu', 'data',
    ];

    protected function casts(): array
    {
        return [
            'lu'   => 'boolean',
            'data' => 'array',
        ];
    }

    // Relation avec User (destinataire)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope: non lues seulement
    public function scopeNonLues($q)
    {
        return $q->where('lu', false);
    }

    // Marquer comme lue
    public function marquerLue(): void
    {
        $this->update(['lu' => true, 'etat' => 'lu']);
    }
}
