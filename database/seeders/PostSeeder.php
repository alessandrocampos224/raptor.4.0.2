<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Certifique-se de que existem categorias
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }

        // Criar posts específicos para cada categoria
        $categories = Category::all();
        
        foreach ($categories as $category) {
            // Criar 2-5 posts publicados por categoria
            $this->createPostsForCategory($category, PostStatus::PUBLISHED->value, rand(2, 5));
            
            // Criar 1-3 posts em rascunho por categoria
            $this->createPostsForCategory($category, PostStatus::DRAFT->value, rand(1, 3));
        }
        
        // Criar alguns posts adicionais aleatórios
        Post::factory(10)->create();
    }
    
    private function createPostsForCategory(Category $category, string $status, int $count): void
    {
        $titles = $this->getTitlesForCategory($category->name, $count);
        
        foreach ($titles as $title) {
            Post::create([
                'category_id' => $category->id,
                'name' => $title,
                'slug' => Str::slug($title),
                'description' => fake()->paragraph(rand(2, 5)),
                'keywords' => implode(', ', fake()->words(rand(3, 6))),
                'image' => 'https://source.unsplash.com/random/800x600/?' . Str::slug($category->name) . '&' . rand(1, 1000),
                'status' => $status,
            ]);
        }
    }
    
    private function getTitlesForCategory(string $categoryName, int $count): array
    {
        $titles = [];
        
        switch ($categoryName) {
            case 'Tecnologia':
                $titles = [
                    'As 10 tendências tecnológicas para ' . date('Y'),
                    'Como a Inteligência Artificial está transformando o mercado',
                    'O futuro da computação quântica',
                    'Guia completo sobre cibersegurança',
                    'Blockchain além das criptomoedas',
                    'Realidade Virtual e Aumentada: aplicações práticas',
                    'O impacto do 5G no cotidiano',
                    'Cloud Computing: vantagens e desafios',
                ];
                break;
                
            case 'Saúde e Bem-estar':
                $titles = [
                    'Hábitos diários para uma vida mais saudável',
                    'O poder da meditação na redução do estresse',
                    'Alimentação consciente: princípios básicos',
                    'Exercícios físicos para fazer em casa',
                    'O impacto do sono na saúde mental',
                    'Mindfulness: prática e benefícios',
                    'Suplementação: quando é realmente necessária?',
                    'Saúde mental no ambiente de trabalho',
                ];
                break;
                
            case 'Educação':
                $titles = [
                    'Metodologias ativas de ensino',
                    'O futuro da educação a distância',
                    'Tecnologias educacionais inovadoras',
                    'Como desenvolver o pensamento crítico',
                    'Educação inclusiva: desafios e soluções',
                    'Aprendizagem baseada em projetos',
                    'O papel do professor na era digital',
                    'Neurociência aplicada à educação',
                ];
                break;
                
            default:
                // Para outras categorias, gerar títulos genéricos
                for ($i = 0; $i < $count + 3; $i++) {
                    $titles[] = fake()->sentence(rand(5, 10));
                }
        }
        
        // Embaralhar e limitar ao número solicitado
        shuffle($titles);
        return array_slice($titles, 0, $count);
    }
} 