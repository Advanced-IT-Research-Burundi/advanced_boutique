<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommandeDetails extends Model
{
    use HasFactory;
    use SoftDeletes;
    

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

    // add column deleted at if it does not exist on schema 

    public static function boot()
    {
        parent::boot();

        if (!\Schema::hasColumn((new self)->getTable(), 'deleted_at')) {
            \Schema::table((new self)->getTable(), function ($table) {
                $table->softDeletes();
            });
        }
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
