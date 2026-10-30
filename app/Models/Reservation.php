<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Correspond à la classe Reservations du diagramme UML
class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'personnel_id', 'ressource_id', 'date_debut', 'date_fin',
        'motif', 'statut', 'valide_par', 'valide_par_admin',
        'date_val_chef', 'date_val_admin',
        'commentaire_chef', 'commentaire_admin', 'conflit_detecte',
    ];

    protected function casts(): array
    {
        return [
            'date_debut'      => 'datetime',
            'date_fin'        => 'datetime',
            'date_val_chef'   => 'datetime',
            'date_val_admin'  => 'datetime',
            'conflit_detecte' => 'boolean',
        ];
    }

    // ── Relations UML ────────────────────────────────────────────
    // Personnel demande 0..* Reservations
    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    // Ressource Concerne 0..* Reservations
    public function ressource()
    {
        return $this->belongsTo(Ressource::class);
    }

    // Chef qui valide (Valide 0..* Reservations)
    public function validePar()
    {
        return $this->belongsTo(Personnel::class, 'valide_par');
    }

    public function valideParAdmin()
    {
        return $this->belongsTo(Personnel::class, 'valide_par_admin');
    }

    // Concerne 0..* historique_Emprunt
    public function historiqueEmprunts()
    {
        return $this->hasMany(HistoriqueEmprunt::class);
    }

    // Bon d'emprunt (dernier généré)
    public function bonEmprunt()
    {
        return $this->hasOne(HistoriqueEmprunt::class)->latest();
    }

    // Suggestions IA liées à cette réservation
    public function aiSuggestions()
    {
        return $this->hasMany(AiSuggestion::class);
    }

    // ── Scopes ───────────────────────────────────────────────────
    public function scopeEnAttente($q)  { return $q->where('statut', 'en_attente'); }
    public function scopeValides($q)    { return $q->where('statut', 'valide_admin'); }
    public function scopeRejetes($q)    { return $q->whereIn('statut', ['rejete_chef','rejete_admin']); }
    public function scopeActives($q) {
        return $q->whereIn('statut', ['en_attente','valide_chef','valide_admin']);
    }

    // ── Accessors ────────────────────────────────────────────────
    public function getDureeHeuresAttribute(): int
    {
        return (int) $this->date_debut->diffInHours($this->date_fin);
    }

    public function getBadgeColorAttribute(): string
    {
        return match($this->statut) {
            'en_attente'    => 'warning',
            'valide_chef'   => 'info',
            'valide_admin'  => 'success',
            'rejete_chef',
            'rejete_admin'  => 'danger',
            'annule'        => 'secondary',
            'termine'       => 'dark',
            default         => 'light',
        };
    }

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'en_attente'   => 'En attente',
            'valide_chef'  => 'Validé (chef)',
            'valide_admin' => 'Validé (admin)',
            'rejete_chef'  => 'Rejeté (chef)',
            'rejete_admin' => 'Rejeté (admin)',
            'annule'       => 'Annulé',
            'termine'      => 'Terminé',
            default        => $this->statut,
        };
    }
}

