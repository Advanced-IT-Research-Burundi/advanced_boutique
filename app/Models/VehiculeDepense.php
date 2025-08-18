<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiculeDepense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vehicule_id',
        'amount',
        'date',
        'description',
        'user_id',
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
            'vehicule_id' => 'integer',
            'amount' => 'double',
            'date' => 'datetime',
            'user_id' => 'integer',
        ];
    }

    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
