<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    // REQUISITO: "Creación de modelos para interactuar con la bd".
    // $fillable protege contra asignación masiva de datos.
    protected $fillable = ['name', 'description', 'user_id'];

    /**
     * Relación Maestro-Detalle (Uno a Muchos).
     * Una Categoría tiene muchos Documentos.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
