<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'filename' => $this->faker->slug() . '.pdf',
            'original_filename' => $this->faker->word() . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(1024, 10485760), // 1KB to 10MB
            'file_path' => 'documents/' . $this->faker->uuid() . '.pdf',
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'is_public' => $this->faker->boolean(20), // 20% chance of being public
        ];
    }
}
