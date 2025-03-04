<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Database\Seeders;

use App\Enums\CategoryStatus;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Tecnologia',
            'Saúde e Bem-estar',
            'Educação',
            'Negócios',
            'Entretenimento',
            'Esportes',
            'Gastronomia',
            'Viagens',
            'Moda',
            'Ciência',
            'Arte e Cultura',
            'Meio Ambiente',
            'Política',
            'Economia',
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
                'status' => CategoryStatus::PUBLISHED->value,
            ]);
        }

        // Criar algumas categorias com status DRAFT
        Category::factory(3)->create([
            'status' => CategoryStatus::DRAFT->value,
        ]);
    }
} 