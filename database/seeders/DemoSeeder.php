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
        // Crear usuarios de prueba
        $user1 = User::create([
            'name' => 'Admin User',
            'email' => 'admin@bovedadocumentos.com',
            'password' => Hash::make('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Test User',
            'email' => 'test@bovedadocumentos.com',
            'password' => Hash::make('password123'),
        ]);

        // Crear categorías para user1
        $categoriaFacturas = Category::create([
            'name' => 'Facturas',
            'description' => 'Facturas y documentos fiscales',
            'user_id' => $user1->id,
        ]);

        $categoriaContratos = Category::create([
            'name' => 'Contratos',
            'description' => 'Contratos y acuerdos legales',
            'user_id' => $user1->id,
        ]);

        $categoriaPersonal = Category::create([
            'name' => 'Documentos Personales',
            'description' => 'Identificaciones y documentos personales',
            'user_id' => $user1->id,
        ]);

        // Crear categorías para user2
        $categoriaProyectos = Category::create([
            'name' => 'Proyectos',
            'description' => 'Documentación de proyectos',
            'user_id' => $user2->id,
        ]);

        $this->command->info('✅ Usuarios y categorías de prueba creados');
        $this->command->info('');
        $this->command->info('📧 Credenciales de prueba:');
        $this->command->info('   Usuario 1: admin@bovedadocumentos.com / password123');
        $this->command->info('   Usuario 2: test@bovedadocumentos.com / password123');
    }
}
