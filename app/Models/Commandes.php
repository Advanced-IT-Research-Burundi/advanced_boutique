<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commandes extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicule_id',
        'matricule',
        'commentaire',
        'poids',
        'date_livraison',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'poids' => 'float',
            'date_livraison' => 'date',
        ];
    }
    /**
     * Get the details associated with the commande.
     */
    public function details()
    {
        return $this->hasMany(CommandeDetails::class, 'commande_id', 'id');
    }
}
