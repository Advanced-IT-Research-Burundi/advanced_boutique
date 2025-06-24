<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'location',
        'description',
        'agency_id',
        'created_by',
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
            'agency_id' => 'integer',
            'created_by' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function userStocks(): HasMany
    {
        return $this->hasMany(UserStock::class);
    }

    public function stockProducts(): HasMany
    {
        return $this->hasMany(StockProduct::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function stockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class);
    }

    public function cashRegisters(): HasMany
    {
        return $this->hasMany(CashRegister::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_stocks')
                    ->withTimestamps()
                    ->withPivot('agency_id', 'created_by');
    }



    public function scopeWithProductCount($query)
    {
        return $query->withCount('products');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'stock_products', 'stock_id', 'product_id')
                    ->withTimestamps();
    }


    /**
     * Scope pour filtrer par agence
     */
    public function scopeByAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    /**
     * Scope pour filtrer par utilisateur créateur
     */
    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('created_by', $creatorId);
    }

    /**
     * Scope pour filtrer par utilisateur assigné
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour recherche globale
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Accessor pour obtenir le nom complet avec localisation
     */
    public function getFullNameAttribute()
    {
        return $this->location ? "{$this->name} ({$this->location})" : $this->name;
    }

    /**
     * Accessor pour vérifier si le stock est assigné
     */
    public function getIsAssignedAttribute()
    {
        return !is_null($this->user_id);
    }

    /**
     * Accessor pour obtenir le statut d'assignation
     */
    public function getAssignmentStatusAttribute()
    {
        return $this->user_id ? 'Assigné' : 'Non assigné';
    }



}

