<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AccessRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'dependency_id',
        'user_id',
        'resource_type',
        'resource_id',
        'status',
        'justification',
        'authorized_by',
        'authorized_at',
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
    ];

    public function dependency(): BelongsTo
    {
        return $this->belongsTo(Dependency::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // Requester
    }

    public function authorizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function resource(): MorphTo
    {
        return $this->morphTo();
    }
}
