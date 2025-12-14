<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear Usuario Admin
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@bovedadocumentos.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
            'role' => User::ROLE_ADMIN,
        ]);

        // 2. Crear Usuario de Prueba (Ciudadano)
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@bovedadocumentos.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_USER,
        ]);

        // 3. Crear Usuario Dependencia
        $dependency = User::factory()->create([
            'name' => 'Dependency User',
            'email' => 'dependency@bovedadocumentos.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_DEPENDENCY,
        ]);

        // 4. Crear Categorías Globales
        $this->call(GlobalCategoriesSeeder::class);

        // 5. Asignar Dependencia a categorías
        $categories = Category::all();
        // Asignar las primeras 2 categorías a la dependencia
        $dependency->accessibleCategories()->sync($categories->take(2)->pluck('id'));

        // 6. Crear algunos documentos de ejemplo para el usuario de prueba
        foreach ($categories->take(2) as $category) {
            Document::factory(2)->create([
                'category_id' => $category->id,
                'user_id' => $user->id,
            ]);
        }

        // 7. Crear algunos documentos sin categoría
        Document::factory(2)->create([
            'category_id' => null,
            'user_id' => $user->id,
        ]);

        $this->command->info('✅ Datos de prueba generados exitosamente');
        $this->command->info('   - 3 Usuarios (admin, test, dependency)');
        $this->command->info('   - 4 Categorías Globales');
        $this->command->info('   - ~6 Documentos');
        $this->command->info('');
        $this->command->info('📧 Credenciales (Password: password123):');
        $this->command->info('   Admin:      admin@bovedadocumentos.com');
        $this->command->info('   User:       test@bovedadocumentos.com');
        $this->command->info('   Dependency: dependency@bovedadocumentos.com');
    }
}
