<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Correspond à la classe Type_personnel du diagramme UML
class TypePersonnel extends Model
{
    use HasFactory;

    protected $table = 'type_personnels';

    protected $fillable = ['libelle', 'description'];

    // 1 TypePersonnel est associé à 1..2 Personnels (UML)
    public function personnels()
    {
        return $this->hasMany(Personnel::class, 'type_personnel_id');
    }
}
