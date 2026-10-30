<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Correspond à la classe Tags du diagramme UML
class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['libelle'];

    // Tags est tagé par 0..* Ressources (many-to-many UML)
    public function ressources()
    {
        return $this->belongsToMany(Ressource::class, 'ressource_tag');
    }
}
