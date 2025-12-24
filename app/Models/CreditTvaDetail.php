<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditTvaDetail extends Model
{
    use HasFactory;
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'credit_tva_id',
        'type',
        'montant',
        'sale_id',
        'user_id',
        'description',
        'date',
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
            'montant' => 'double',
            'date' => 'date',
        ];
    }
    
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // Custom logic before creating a CreditTvaDetail
            $creditTva = CreditTva::firstOrCreate(
                [
                    'date' => now()->toDateString(),
                    'montant' => 0,
                    'description' => 'Montant Total de TVA',
                    'is_actif' => true,
                    ]
                );
                $model->user_id = auth()->id();
                if ($model->type === 'ADD') {
                    // Logic for ADD type
                    $creditTva->montant += $model->montant;
                    $creditTva->save();
                }elseif ($model->type === 'SUB') {
                    // Logic for SUB type
                    $creditTva->montant -= $model->montant;
                    $creditTva->save();
                }
            });
            
            static::updating(function ($model) {
                // Custom logic before updating a CreditTvaDetail
            });
        }
    }