<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'stock_id',
        'agency_id',
        'created_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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
     * Relation avec l'agence
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Relation avec l'utilisateur créateur
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
}
