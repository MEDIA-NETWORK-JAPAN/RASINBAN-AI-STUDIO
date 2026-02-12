<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function planLimits(): HasMany
    {
        return $this->hasMany(PlanLimit::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
