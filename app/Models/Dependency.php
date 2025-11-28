<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dependency extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description'];

    /**
     * Users belonging to this dependency.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Access requests made by this dependency.
     */
    public function accessRequests(): HasMany
    {
        return $this->hasMany(AccessRequest::class);
    }

    /**
     * Active permissions granted to this dependency.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(DependencyPermission::class);
    }

    /**
     * Access logs for this dependency.
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(DocumentAccessLog::class);
    }
}
