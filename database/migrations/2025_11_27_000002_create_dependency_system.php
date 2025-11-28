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
        // 1. Dependencies
        Schema::create('dependencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable(); // Identificador único opcional
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Link Users to Dependencies
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('dependency_id')->nullable()->constrained('dependencies')->nullOnDelete();
        });

        // 3. Access Requests
        Schema::create('access_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dependency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained(); // Solicitante (usuario de la dependencia)

            // Recurso solicitado (Documento o Categoría)
            // resource_type = 'App\Models\Document' o 'App\Models\Category'
            $table->morphs('resource');

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('justification')->nullable();

            // Quién autorizó
            $table->foreignId('authorized_by')->nullable()->constrained('users');
            $table->timestamp('authorized_at')->nullable();

            $table->timestamps();
        });

        // 4. Dependency Permissions (Granted Access)
        Schema::create('dependency_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dependency_id')->constrained()->cascadeOnDelete();

            // Permiso sobre qué? (Documento o Categoría)
            $table->morphs('permissionable');

            $table->timestamp('granted_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate permissions
            $table->unique(['dependency_id', 'permissionable_type', 'permissionable_id'], 'dep_perm_unique');
        });

        // 5. Document Access Logs
        Schema::create('document_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dependency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('accessed_at')->useCurrent();
            $table->string('action')->default('view'); // view, download, print_attempt
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_access_logs');
        Schema::dropIfExists('dependency_permissions');
        Schema::dropIfExists('access_requests');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['dependency_id']);
            $table->dropColumn('dependency_id');
        });

        Schema::dropIfExists('dependencies');
    }
};
