<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Correspond à la classe Bureau du diagramme UML
class Bureau extends Model
{
    use HasFactory;

    protected $table = 'bureaus';

    protected $fillable = ['service_id', 'nom', 'description'];

    // 1 Bureau appartient à 1 Service (UML)
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // 1 Bureau est possédé par 1 Personnel (UML: est possédé)
    public function personnels()
    {
        return $this->hasMany(Personnel::class);
    }
}

