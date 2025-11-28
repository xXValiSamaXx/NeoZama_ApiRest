<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    // REQUISITO: "Creación de modelos para interactuar con la bd".
    // $fillable protege contra asignación masiva de datos.
    protected $fillable = ['name', 'description'];

    /**
     * Relación con Documentos (Muchos a Muchos).
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'category_document');
    }
}
