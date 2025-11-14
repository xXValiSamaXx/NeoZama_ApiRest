<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
            'category_id' => 'nullable|exists:categories,id',
            'is_public' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título del documento es obligatorio.',
            'file.required' => 'Debe seleccionar un archivo.',
            'file.max' => 'El archivo no debe superar los 10MB.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
        ];
    }
}
