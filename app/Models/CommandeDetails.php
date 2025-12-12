<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeDetails extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'quantite' => 'double',
            'poids' => 'double',
            'prix_unitaire' => 'double',
            'remise' => 'float',
            'date_livraison' => 'date',
        ];
    }

    public function commande()
    {
        return $this->belongsTo(Commandes::class, 'commande_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(CommandeDetails::class, 'commande_id', 'id');
    }
}
