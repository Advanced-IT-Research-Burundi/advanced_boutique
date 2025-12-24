<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocalSale extends Model
{
    use HasFactory,SoftDeletes;

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
            'client_id' => 'integer',
            'stock_id' => 'integer',
            'user_id' => 'integer',
            'total_amount' => 'decimal',
            'paid_amount' => 'decimal',
            'due_amount' => 'decimal',
            'sale_date' => 'datetime',
            'agency_id' => 'integer',
            'created_by' => 'integer',
        ];
    }

    protected $appends = ['numero'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }


    public static function boot()
    {
        parent::boot();

        static::creating(function ($localSale) {
            // Set default values or perform actions before creating a LocalSale
            
        });

        static::deleting(function ($localSale) {
            // Delete related sale items

        });

        static::created(function ($localSale) {
            // Actions after creating a LocalSale
            CreditTvaDetail::create([
                'montant' => $localSale->total_tva,
                'sale_id' => $localSale->id,
                'description' => 'Vente local A N° #'.$localSale->id,
                'date' => now(),
                'type' => 'SUB',
            ]);
        });
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(LocalSaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    public function getNumeroAttribute()
    {
        return substr($this->stock->name, 0, 2) . '/' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
