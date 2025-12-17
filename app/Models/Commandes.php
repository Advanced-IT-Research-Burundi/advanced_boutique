<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commandes extends Model
{
    use HasFactory,SoftDeletes;

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
        'exchange_rate',
        'user_id',
        'currency',
        'status',
        'description',
    ];
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->status = 'pending'; // Default status when creating a new commande
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'vehicule_id' => 'integer',
            'poids' => 'float',
            'date_livraison' => 'date',
        ];
    }

    // protected $with = ['details', 'vehicule'];
    /**
     * Get the details associated with the commande.
     */
    public function details()
    {
        return $this->hasMany(CommandeDetails::class, 'commande_id', 'id');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id', 'id');
    }

    public function depenses()
    {
        return $this->hasMany(DepensesImportation::class, 'commande_id', 'id');
    }

    /**
     * Get the total depenses for this commande.
     */
    public function getRepportCommande()
    {
        // Prix total du details commande

        $totalAchat = $this->details->sum(function ($detail) {
            return round($detail->total_price);
        });
        return [
            'fournisseur' => $totalAchat,
        ];
    }

}
