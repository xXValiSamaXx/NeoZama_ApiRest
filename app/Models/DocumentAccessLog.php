<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAccessLog extends Model
{
    use HasFactory;

    // We don't need timestamps() as we have accessed_at manually and no update_at needed basically
    public $timestamps = false;

    protected $fillable = [
        'document_id',
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'accessed_at',
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
