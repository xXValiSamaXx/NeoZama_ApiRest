<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'dependency_id',
        'user_id',
        'document_id',
        'reason',
        'status',
        'valid_until',
        'approved_at',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function dependency(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dependency_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
