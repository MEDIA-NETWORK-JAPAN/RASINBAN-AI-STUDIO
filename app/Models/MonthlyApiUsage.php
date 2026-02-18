<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyApiUsage extends Model
{
    /** @use HasFactory<\Database\Factories\MonthlyApiUsageFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'dify_app_id',
        'usage_month',
        'endpoint',
        'request_count',
        'last_request_at',
    ];

    protected function casts(): array
    {
        return [
            'last_request_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function difyApp(): BelongsTo
    {
        return $this->belongsTo(DifyApp::class);
    }
}
