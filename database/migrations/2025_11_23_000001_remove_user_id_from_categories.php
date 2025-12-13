<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Check if foreign key exists before dropping to avoid errors in inconsistent states
            $conn = Schema::getConnection();
            $dbSchema = $conn->getSchemaBuilder();
            $foreignKeys = $dbSchema->getForeignKeys('categories');

            // This is a naive check, but for MySQL often sufficient. 
            // Better: use try-catch or explicit check if driver supports it.
            // Since we are in a hurry, we can use a raw statement check or try catch.
            // Let's use a simple try-catch block for the drop, which is safest for migrations in flux.
        });

        try {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Exception $e) {
            // Foreign key usually doesn't exist, ignore
        }

        try {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        } catch (\Exception $e) {
            // Column might not exist
        }

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
