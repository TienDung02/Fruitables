<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Currency extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use  HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'locale',
        'exchange_rate',
        'decimal_places',
        'position',
        'is_default',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'decimal_places' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean'
    ];

    protected $dates = ['deleted_at'];
    public $timestamps = true;

    public static function getByLocale($locale)
    {
        return static::where('locale', $locale)->where('is_active', true)->first();
    }

    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }
}
