<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DependencyPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'dependency_id',
        'permissionable_type',
        'permissionable_id',
        'granted_at',
        'expires_at',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function dependency(): BelongsTo
    {
        return $this->belongsTo(Dependency::class);
    }

    public function permissionable(): MorphTo
    {
        return $this->morphTo();
    }
}
