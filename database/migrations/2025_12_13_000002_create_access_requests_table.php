<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('access_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dependency_id')->constrained('users')->onDelete('cascade'); // The requester
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');       // The owner
            $table->foreignId('document_id')->nullable()->constrained()->onDelete('cascade'); // Specific doc or null for general? Let's assume specific for now as per prompt "access to certain documents"
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_requests');
    }
};
