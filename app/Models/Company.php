<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tp_name',
        'tp_type',
        'tp_TIN',
        'tp_trade_number',
        'tp_postal_number',
        'tp_phone_number',
        'tp_address_privonce',
        'tp_address_avenue',
        'tp_address_quartier',
        'tp_address_commune',
        'tp_address_rue',
        'tp_address_number',
        'vat_taxpayer',
        'ct_taxpayer',
        'tl_taxpayer',
        'tp_fiscal_center',
        'tp_activity_sector',
        'tp_legal_form',
        'payment_type',
        'is_actif',
        'user_id',
        'tp_email',
        'tp_website',
        'tp_logo',
        'tp_bank',
        'tp_account_number',
        'tp_facebook',
        'tp_twitter',
        'tp_instagram',
        'tp_youtube',
        'tp_whatsapp',
        'tp_address',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'is_actif' => 'boolean',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    /**
     * Get the current active company.
     *
     * @return \App\Models\Company
     */
    public static function current()
    {
        return self::where('is_actif', true)->first();
    }
}
