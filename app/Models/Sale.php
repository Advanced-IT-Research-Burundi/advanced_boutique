<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'stock_id',
        'user_id',
        'total_amount',
        'paid_amount',
        'due_amount',
        'sale_date',
        'status',
        'description',
        'type_facture',
        'agency_id',
        'created_by',
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

    public static function boot()
    {
        parent::boot();

        static::creating(function ($localSale) {
            // Set default values or perform actions before creating a LocalSale
            CreditTvaDetail::create([
                'montant' => $localSale->total_tva,
                'sale_id' => $localSale->id,
                'description' => 'Vente  B N° #' . $localSale->id,
                'date' => now(),
                'type' => 'SUB',
            ]);

            $lastNumber = self::where('stock_id', $localSale->stock_id)
            ->max('stock_sequence');

            $localSale->stock_sequence = ($lastNumber ?? 0) + 1;
        });
        static::deleting(function ($localSale) {
            // Delete related sale items

        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
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
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

        public function getNumeroAttribute()
        {
            if ($this->stock_sequence === null) {
                return '#' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
            }

            return substr($this->stock->name, 0, 2)
                . '-'
                . str_pad($this->stock_sequence, 4, '0', STR_PAD_LEFT);
        }
}
