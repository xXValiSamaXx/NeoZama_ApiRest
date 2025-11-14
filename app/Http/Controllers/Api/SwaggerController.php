<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="Usuario",
 *     required={"id","name","email"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Juan Pérez"),
 *     @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Categoría",
 *     required={"id","name","user_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Facturas"),
 *     @OA\Property(property="description", type="string", example="Facturas del año 2024"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="Document",
 *     type="object",
 *     title="Documento",
 *     required={"id","title","filename","user_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Contrato 2024"),
 *     @OA\Property(property="description", type="string", example="Contrato de servicios"),
 *     @OA\Property(property="filename", type="string", example="abc123.pdf"),
 *     @OA\Property(property="original_filename", type="string", example="contrato.pdf"),
 *     @OA\Property(property="mime_type", type="string", example="application/pdf"),
 *     @OA\Property(property="file_size", type="integer", example=1024000),
 *     @OA\Property(property="file_path", type="string", example="documents/abc123.pdf"),
 *     @OA\Property(property="category_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="is_public", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class SwaggerController extends Controller
{
    // Este controlador solo contiene las definiciones de esquemas para Swagger
}
