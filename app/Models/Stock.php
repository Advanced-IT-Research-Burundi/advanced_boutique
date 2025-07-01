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

    public function proformas(): HasMany
    {
        return $this->hasMany(Proforma::class);
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
/**
     * Vérifier si un utilisateur a accès à ce stock
     */
    public function hasUser($userId)
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    /**
     * Obtenir le nombre d'utilisateurs assignés
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Scope pour les stocks avec des utilisateurs assignés
     */
    public function scopeWithUsers($query)
    {
        return $query->whereHas('users');
    }

    /**
     * Scope pour les stocks sans utilisateurs assignés
     */
    public function scopeWithoutUsers($query)
    {
        return $query->whereDoesntHave('users');
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour les stocks actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope pour filtrer par agence
     */
    public function scopeByAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    /**
     * Accessor pour le pourcentage de capacité utilisée
     */
    public function getCapacityPercentageAttribute()
    {
        if ($this->capacity <= 0) {
            return 0;
        }

        return round(($this->current_quantity / $this->capacity) * 100, 2);
    }

    /**
     * Accessor pour vérifier si le stock est plein
     */
    public function getIsFullAttribute()
    {
        return $this->current_quantity >= $this->capacity;
    }

    /**
     * Accessor pour vérifier si le stock est vide
     */
    public function getIsEmptyAttribute()
    {
        return $this->current_quantity <= 0;
    }

    /**
     * Accessor pour le statut de disponibilité
     */
    public function getAvailabilityStatusAttribute()
    {
        if ($this->status !== 'active') {
            return 'inactive';
        }

        $percentage = $this->capacity_percentage;

        if ($percentage >= 90) {
            return 'full';
        } elseif ($percentage >= 70) {
            return 'high';
        } elseif ($percentage >= 30) {
            return 'medium';
        } elseif ($percentage > 0) {
            return 'low';
        } else {
            return 'empty';
        }
    }
}
