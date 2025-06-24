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

    protected $dates = ['deleted_at'];

    /**
     * Relation avec User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec Stock
     */
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Relation avec Agency
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Relation avec le créateur
     */
    public function creator()
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
     * Scope pour filtrer par agence
     */
    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    /**
     * Scope pour obtenir les stocks actifs
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }
}
