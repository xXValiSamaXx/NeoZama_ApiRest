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
        ]);

        // 2. Crear Usuario de Prueba
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@bovedadocumentos.com',
            'password' => Hash::make('password123'),
        ]);

        // 3. Crear Categorías y Documentos para Admin
        Category::factory(5)->create(['user_id' => $admin->id])->each(function ($category) use ($admin) {
            Document::factory(3)->create([
                'category_id' => $category->id,
                'user_id' => $admin->id,
            ]);
        });

        // 4. Crear Categorías por defecto para el usuario de prueba
        $defaultCategories = [
            'INE' => 'Credencial para votar',
            'CURP' => 'Clave Única de Registro de Población',
            'Acta de Nacimiento' => 'Documento de identidad',
            'RFC' => 'Registro Federal de Contribuyentes',
            'Comprobante de Domicilio' => 'Luz, Agua o Teléfono',
        ];

        foreach ($defaultCategories as $name => $description) {
            Category::factory()->create([
                'name' => $name,
                'description' => $description,
                'user_id' => $user->id,
            ]);
        }
        Document::factory(2)->create([
            'category_id' => null,
            'user_id' => $user->id,
        ]);

        $this->command->info('✅ Datos de prueba generados exitosamente');
        $this->command->info('   - 2 Usuarios (admin, test)');
        $this->command->info('   - 10 Categorías');
        $this->command->info('   - ~32 Documentos');
        $this->command->info('');
        $this->command->info('📧 Credenciales:');
        $this->command->info('   Admin: admin@bovedadocumentos.com / password123');
        $this->command->info('   User:  test@bovedadocumentos.com / password123');
    }
}
