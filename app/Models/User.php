<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'profile_photo',
        'status',
        'role',
        'permissions',
        'last_login_at',
        'must_change_password',
        'two_factor_enabled',
        'two_factor_secret',
        'recovery_codes',
        'company_id',
        'agency_id',
        'created_by',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'email_verified_at' => 'timestamp',
            'date_of_birth' => 'date',
            'permissions' => 'array',
            'last_login_at' => 'timestamp',
            'must_change_password' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'recovery_codes' => 'array',
            'company_id' => 'integer',
            'agency_id' => 'integer',
            'created_by' => 'integer',
            'user_id' => 'integer',
        ];
    }


    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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
        return $this->belongsTo(User::class, 'created_by');
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function agencies(): HasMany
    {
        return $this->hasMany(Agency::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }


    public function userStocks(): HasMany
    {
        return $this->hasMany(UserStock::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function cashRegisters(): HasMany
    {
        return $this->hasMany(CashRegister::class);
    }

    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function expenseTypes(): HasMany
    {
        return $this->hasMany(ExpenseType::class);
    }

    public function stockTransfers(): HasMany
    {
        return $this->hasMany(StockTransfer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }



    /**
     * Relation many-to-many avec Stock via UserStock
     */
    public function assignedStocks()
    {
        return $this->belongsToMany(Stock::class, 'user_stocks')
                    ->withPivot('agency_id', 'created_by')
                    ->withTimestamps()
                    ->whereNull('user_stocks.deleted_at');
    }

    /**
     * Obtenir les stocks disponibles pour l'utilisateur dans son agence
     */
    public function getAvailableStocksAttribute()
    {
        return $this->assignedStocks()
                    ->where('stocks.agency_id', $this->agency_id)
                    ->orderBy('stocks.created_at', 'desc')
                    ->get();
    }

}
