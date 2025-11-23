<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // Crear categorías globales
        $categories = [
            ['name' => 'Acta de Nacimiento', 'description' => 'Documento de identidad oficial'],
            ['name' => 'INE o Pasaporte', 'description' => 'Identificación oficial vigente'],
            ['name' => 'CURP', 'description' => 'Clave Única de Registro de Población'],
            ['name' => 'Comprobante de Domicilio', 'description' => 'Recibo de luz, agua o teléfono'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }
};
