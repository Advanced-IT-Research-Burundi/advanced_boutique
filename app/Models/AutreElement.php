<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutreElement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'libelle',
        'emplacement',
        'quantite',
        'valeur',
        'devise',
        'type_element',
        'reference',
        'observation',
        'exchange_rate',
        'document',
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
            'date' => 'date',
            'quantite' => 'decimal:2',
            'valeur' => 'decimal:2',
        ];
    }

    public function getDocumentAttribute($v)
    {
        if ($v) {
            return asset($v);
        }
        return null;
    }
}
