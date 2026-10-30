<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Correspond à la classe Personnel du diagramme UML
class Personnel extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'type_personnel_id',
        'division_id',
        'service_id',
        'bureau_id',
        'nom',
        'prenom',
        'statut',
    ];

    protected function casts(): array
    {
        return ['statut' => 'boolean'];
    }

    // ── Relation avec User (Breeze) ──────────────────────────────
    // Personnel belongsTo User — id_user est la FK
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // ── Relations UML ────────────────────────────────────────────
    // Personnel possède 1 TypePersonnel
    public function typePersonnel()
    {
        return $this->belongsTo(TypePersonnel::class, 'type_personnel_id');
    }

    // Personnel appartient à 1 Division
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // Personnel appartient à 1 Service
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Personnel est possédé par 1 Bureau
    public function bureau()
    {
        return $this->belongsTo(Bureau::class);
    }

    // Personnel demande 0..* Reservations
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Personnel reçoit 0..* Notifications (via User)
    public function notificationsApp()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id_user');
    }

    // Réservations validées par ce personnel (rôle chef)
    public function reservationsValidees()
    {
        return $this->hasMany(Reservation::class, 'valide_par');
    }

    // ── Accessors pratiques ──────────────────────────────────────
    // Accéder à l'email via personnel->email
    public function getEmailAttribute()
    {
        return $this->user?->email;
    }

    // Accéder au matricule via personnel->matricule
    public function getMatriculeAttribute()
    {
        return $this->user?->matricule;
    }

    // Nom complet
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Initiales
    public function getInitialesAttribute()
    {
        return strtoupper(substr($this->prenom, 0, 1) . substr($this->nom, 0, 1));
    }
}
