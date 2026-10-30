<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Correspond à la classe Division du diagramme UML
class Division extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'code', 'description', 'chef_id'];

    // Le chef de la division est un Personnel
    public function chef()
    {
        return $this->belongsTo(Personnel::class, 'chef_id');
    }

    // 1 Division comprend 0..* Services (UML)
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // 1 Division possède 0..* Ressources (UML)
    public function ressources()
    {
        return $this->hasMany(Ressource::class);
    }

    // Personnels appartenant à cette division
    public function personnels()
    {
        return $this->hasMany(Personnel::class);
    }
}
