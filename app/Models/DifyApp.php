<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DifyApp extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'api_key',
        'base_url',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }

    public function monthlyApiUsages(): HasMany
    {
        return $this->hasMany(MonthlyApiUsage::class);
    }
}
