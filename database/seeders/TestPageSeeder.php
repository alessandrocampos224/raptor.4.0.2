<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class TestPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::create([
            'name' => 'Página de Teste',
            'slug' => 'pagina-de-teste',
            'title' => 'Título da Página de Teste',
            'status' => 'published',
            'tipo' => 'content',
            'config' => [
                'components' => [
                    [
                        'type' => 'header',
                        'props' => [
                            'title' => 'Cabeçalho da Página de Teste',
                            'subtitle' => 'Subtítulo do cabeçalho',
                            'alignment' => 'center',
                            'backgroundColor' => '#f8f9fa',
                            'textColor' => '#212529'
                        ]
                    ],
                    [
                        'type' => 'content',
                        'props' => [
                            'content' => '<p>Este é um conteúdo de teste para a página.</p>',
                            'alignment' => 'left',
                            'backgroundColor' => '#ffffff',
                            'textColor' => '#212529'
                        ]
                    ]
                ],
                'globalStyles' => [
                    'theme' => 'light',
                    'spacing' => 'normal',
                    'contentWidth' => 'contained'
                ]
            ]
        ]);
    }
} 