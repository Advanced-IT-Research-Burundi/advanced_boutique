<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proforma extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'stock_id',
        'user_id',
        'total_amount',
        'due_amount',
        'sale_date',
        'note',
        'invoice_type',
        'agency_id',
        'created_by',
        'proforma_items',
        'client',
        'is_valid'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'proforma_items' => 'array',
        'client' => 'array'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'sale_date'
    ];

    // protected $appends 

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getPaidAmountAttribute()
    {
        return $this->total_amount - $this->due_amount;
    }

    public function getStatusAttribute()
    {
        if ($this->due_amount == 0) {
            return 'paid';
        } elseif ($this->due_amount < $this->total_amount) {
            return 'partial';
        } else {
            return 'unpaid';
        }
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'paid':
                return 'Payé';
            case 'partial':
                return 'Partiel';
            case 'unpaid':
                return 'Impayé';
            default:
                return 'Inconnu';
        }
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'paid':
                return 'success';
            case 'partial':
                return 'warning';
            case 'unpaid':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('due_amount', 0);
    }

    public function scopePartial($query)
    {
        return $query->where('due_amount', '>', 0)
                    ->whereRaw('due_amount < total_amount');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereRaw('due_amount = total_amount');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', now()->year);
    }

    // Methods
    public function getItems()
    {
        return $this->proforma_items ?? [];
    }

    public function getClientData()
    {
        return json_decode($this->client, true) ?? [];
    }

    public function getTotalQuantity()
    {
        $items = $this->getItems();
        return collect($items)->sum('quantity');
    }

    public function getTotalDiscount()
    {
        $items = $this->getItems();
        return collect($items)->sum('discount');
    }

    public function getFormattedNumber()
    {
        return 'PRO-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function markAsPaid()
    {
        $this->update(['due_amount' => 0]);
    }

    public function addPayment($amount)
    {
        $newDueAmount = max(0, $this->due_amount - $amount);
        $this->update(['due_amount' => $newDueAmount]);
        return $this->due_amount == 0;
    }
}
