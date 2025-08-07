<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepensesImportation extends Model
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
            'depense_importation_type_id' => 'integer',
            'exchange_rate' => 'double',
            'amount' => 'double',
            'amount_currency' => 'double',
            'date' => 'datetime',
            'user_id' => 'integer',
        ];
    }

    public function depenseImportationType(): BelongsTo
    {
        return $this->belongsTo(DepenseImportationType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
