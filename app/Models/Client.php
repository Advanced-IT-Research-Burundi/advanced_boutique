<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_type',
        'nif',
        'societe',
        'name',
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'balance',
        'agency_id',
        'created_by',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function getFullNameAttribute(): string
    {
        if ($this->patient_type === 'physique') {
            return trim($this->first_name . ' ' . $this->last_name);
        }

        return $this->name;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->patient_type === 'physique') {
            return $this->getFullNameAttribute();
        }

        return $this->societe ?? $this->name;
    }

    public function scopePhysique($query)
    {
        return $query->where('patient_type', 'physique');
    }

    public function scopeMoral($query)
    {
        return $query->where('patient_type', 'moral');
    }

    public function scopeWithPositiveBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    public function scopeWithNegativeBalance($query)
    {
        return $query->where('balance', '<', 0);
    }

    public function scopeByAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }


    public function isPhysique(): bool
    {
        return $this->patient_type === 'physique';
    }


    public function isMoral(): bool
    {
        return $this->patient_type === 'moral';
    }



    public function addToBalance(float $amount): void
    {
        $this->increment('balance', $amount);
    }

    public function subtractFromBalance(float $amount): void
    {
        $this->decrement('balance', $amount);
    }

    public function resetBalance(): void
    {
        $this->update(['balance' => 0]);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2, ',', ' ') . ' Fr';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (auth()->check() && !$client->created_by) {
                $client->created_by = auth()->id();
            }
        });
    }
}
