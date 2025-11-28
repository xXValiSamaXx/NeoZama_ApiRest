<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'filename',
        'original_filename',
        'mime_type',
        'file_size',
        'file_path',
        // 'category_id', // Removed in migration
        'user_id',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'file_size' => 'integer',
    ];

    /**
     * Relación con las categorías (Muchos a Muchos)
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_document');
    }

    /**
     * Relación con el usuario propietario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Usuarios con los que se ha compartido este documento
     */
    public function sharedWith(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'document_shares', 'document_id', 'shared_with_user_id')
            ->withPivot('permission')
            ->withTimestamps();
    }

    /**
     * Verificar si el usuario puede acceder al documento
     */
    public function canAccess(User $user): bool
    {
        // El propietario siempre puede acceder
        if ($this->user_id === $user->id) {
            return true;
        }

        // Si es público, cualquiera puede acceder
        if ($this->is_public) {
            return true;
        }

        // Verificar si está compartido con el usuario directamente
        if ($this->sharedWith()->where('shared_with_user_id', $user->id)->exists()) {
            return true;
        }

        // Verificar permisos de dependencia
        if ($user->dependency_id) {
            // Permiso específico para este documento
            $hasDocPermission = DependencyPermission::where('dependency_id', $user->dependency_id)
                ->where('permissionable_type', Document::class)
                ->where('permissionable_id', $this->id)
                ->exists();

            if ($hasDocPermission) return true;

            // Permiso por categoría
            // Obtener IDs de categorías de este documento
            $categoryIds = $this->categories()->pluck('categories.id');

            $hasCategoryPermission = DependencyPermission::where('dependency_id', $user->dependency_id)
                ->where('permissionable_type', Category::class)
                ->whereIn('permissionable_id', $categoryIds)
                ->exists();

            if ($hasCategoryPermission) return true;
        }

        return false;
    }

    /**
     * Access Logs for this document
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(DocumentAccessLog::class);
    }

    /**
     * Obtener el tamaño del archivo en formato legible
     */
    public function getReadableFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
