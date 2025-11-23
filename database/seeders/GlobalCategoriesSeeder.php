<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class GlobalCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Acta de Nacimiento',
                'description' => 'Documento de identidad oficial',
            ],
            [
                'name' => 'INE o Pasaporte',
                'description' => 'Identificación oficial vigente',
            ],
            [
                'name' => 'CURP',
                'description' => 'Clave Única de Registro de Población',
            ],
            [
                'name' => 'Comprobante de Domicilio',
                'description' => 'Recibo de luz, agua o teléfono',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }

        $this->command->info('✅ Categorías globales creadas exitosamente');
    }
}
