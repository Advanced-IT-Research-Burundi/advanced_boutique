<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'purchase_price',
        'sale_price_ht',
        'sale_price_ttc',
        'unit',
        'image',
        'alert_quantity',
        'agency_id',
        'created_by',
        'user_id',
    ];

    protected $appends = ['sale_price'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'category_id' => 'integer',
            'purchase_price' => 'decimal',
            'sale_price' => 'decimal',
            'alert_quantity' => 'float',
            'agency_id' => 'integer',
            'created_by' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function getSalePriceAttribute()
    {
        return $this->sale_price_ttc;
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function stockProducts(): HasMany
    {
        return $this->hasMany(StockProduct::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockTransferItems(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }

    // public function stocks(): BelongsToMany
    // {
    //     return $this->belongsToMany(Stock::class);
    // }
     public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'stock_products')
                    // ->withPivot('quantity', 'agency_id')
                    ->withTimestamps();
    }



    /**
     * Obtenir la quantité pour une agence spécifique
     */
    public function getQuantityForAgency($agencyId)
    {
        $stockProduct = $this->stocks()
                            ->wherePivot('agency_id', $agencyId)
                            ->first();

        return $stockProduct ? $stockProduct->pivot->quantity : 0;
    }

    /**
     * Obtenir le stock associé pour une agence spécifique
     */
    public function getStockForAgency($agencyId)
    {
        return $this->stocks()
                   ->wherePivot('agency_id', $agencyId)
                   ->first();
    }
    public function getAvailableStockAttribute()
    {
        return \App\Models\StockProduct::where('product_id', $this->id)->sum('quantity') ?? 0;
    }

    public function getImageAttribute($value)
    {
        return $value ? URL::to('/') .Storage::url($value) : null;
    }
}
