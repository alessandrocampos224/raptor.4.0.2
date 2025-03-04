<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Exibe uma página interna com base no caminho da URL
     *
     * @param string $path Caminho da URL
     * @return \Inertia\Response
     */
    public function show($path)
    {
        try {
            // Tenta encontrar uma página com o slug correspondente ao caminho
            $page = Page::where('slug', $path)->where('status', 'published')->first();

            // Se a página não for encontrada, retorna uma página 404
            if (!$page) {
                return Inertia::render('Errors/404', [
                    'message' => 'Página não encontrada'
                ]);
            }

            // Obtém os dados de configuração da página
            $config = $page->config ?? [];
            
            // Retorna a página interna com os dados da página
            return Inertia::render('internal/base/InternalPage', [
                'pageData' => [
                    'id' => $page->id,
                    'title' => $page->title ?? $page->name,
                    'slug' => $page->slug,
                    'components' => $config['components'] ?? [],
                    'globalStyles' => $config['globalStyles'] ?? [
                        'theme' => 'light',
                        'spacing' => 'normal',
                        'contentWidth' => 'contained'
                    ]
                ],
                'path' => $path,
                'debug' => true
            ]);
        } catch (\Exception $e) {
            // Log do erro
            \Log::error('Erro ao renderizar página interna: ' . $e->getMessage());
            
            // Retorna uma página de erro
            return Inertia::render('Errors/500', [
                'message' => 'Ocorreu um erro ao carregar a página'
            ]);
        }
    }
} 