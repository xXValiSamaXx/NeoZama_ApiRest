<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // Role Constants
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const ROLE_DEPENDENCY = 'dependency';

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN || $this->is_admin;
    }

    /**
     * Check if user is dependency
     */
    public function isDependency(): bool
    {
        return $this->role === self::ROLE_DEPENDENCY;
    }

    /**
     * Solicitudes de acceso enviadas (si es dependencia)
     */
    public function accessRequestsSent(): HasMany
    {
        return $this->hasMany(AccessRequest::class, 'dependency_id');
    }

    /**
     * Solicitudes de acceso recibidas (si es dueño de documento)
     */
    public function accessRequestsReceived(): HasMany
    {
        return $this->hasMany(AccessRequest::class, 'user_id');
    }

    /**
     * Documentos del usuario
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Categorías del usuario
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Documentos compartidos con este usuario
     */
    public function sharedDocuments()
    {
        return $this->belongsToMany(Document::class, 'document_shares', 'shared_with_user_id', 'document_id')
            ->withPivot('permission')
            ->withTimestamps();
    }
    /**
     * Categorías a las que tiene acceso (si es dependencia)
     */
    public function accessibleCategories()
    {
        return $this->belongsToMany(Category::class, 'category_user', 'user_id', 'category_id')
            ->withTimestamps();
    }
}
