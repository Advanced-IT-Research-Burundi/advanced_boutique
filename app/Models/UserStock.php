<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserStock extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'stock_id',
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
            'user_id' => 'integer',
            'stock_id' => 'integer',
            'agency_id' => 'integer',
            'created_by' => 'integer',
        ];
    }
/**
     * Relation avec l'utilisateur assigné
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le stock
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Relation avec l'agence (optionnelle)
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Relation avec l'utilisateur qui a créé l'assignation
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope pour filtrer par utilisateur
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour filtrer par stock
     */
    public function scopeForStock($query, $stockId)
    {
        return $query->where('stock_id', $stockId);
    }

    /**
     * Scope pour filtrer par agence
     */
    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    /**
     * Scope pour les assignations actives (non supprimées)
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
