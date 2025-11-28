<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create pivot table
        Schema::create('category_document', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['document_id', 'category_id']);
        });

        // Migrate existing data (Optional, best effort)
        // We select straight from DB to avoid model issues during migration
        $documents = \Illuminate\Support\Facades\DB::table('documents')->whereNotNull('category_id')->get();
        foreach ($documents as $doc) {
            \Illuminate\Support\Facades\DB::table('category_document')->insert([
                'document_id' => $doc->id,
                'category_id' => $doc->category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop the old column
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
        });

        // Restore data (take first category)
        $relations = \Illuminate\Support\Facades\DB::table('category_document')->get();
        foreach ($relations as $rel) {
            // Only if category_id is null (first one wins)
             \Illuminate\Support\Facades\DB::table('documents')
                ->where('id', $rel->document_id)
                ->whereNull('category_id')
                ->update(['category_id' => $rel->category_id]);
        }

        Schema::dropIfExists('category_document');
    }
};
