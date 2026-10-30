<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Correspond à la classe Service du diagramme UML
class Service extends Model
{
    use HasFactory;

    protected $fillable = ['division_id', 'nom', 'description'];

    // 1 Service appartient à 1 Division (UML)
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // 1 Service regroupe 0..* Bureaux (UML)
    public function bureaux()
    {
        return $this->hasMany(Bureau::class);
    }

    // Personnels de ce service
    public function personnels()
    {
        return $this->hasMany(Personnel::class);
    }
}
