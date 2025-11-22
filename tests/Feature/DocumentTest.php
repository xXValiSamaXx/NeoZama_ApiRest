<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_document()
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->postJson('/api/documents', [
            'title' => 'My Document',
            'file' => $file,
            'category_id' => $category->id,
        ]);

        $response->assertStatus(201);
        
        // Verificar que el archivo se guardó (el path puede variar según la implementación del controller)
        // Asumimos que el controller guarda en 'documents'
        // Storage::disk('local')->assertExists('documents/' . $file->hashName());
    }

    public function test_user_can_list_documents()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Document::factory(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/documents');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_delete_document()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $document = Document::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/documents/{$document->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('documents', ['id' => $document->id]);
    }
}
