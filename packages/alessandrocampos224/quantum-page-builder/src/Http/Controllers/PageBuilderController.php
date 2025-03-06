<?php

namespace AlessandroCampos224\QuantumPageBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class PageBuilderController extends Controller
{
    /**
     * Salvar a estrutura da página
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        try {
            $structure = $request->all();
            
            // Validar a estrutura
            if (!isset($structure['sections']) || !isset($structure['globalStyles'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estrutura inválida'
                ], 400);
            }
            
            // Salvar a estrutura em um arquivo JSON
            Storage::put('page-builder/structure.json', json_encode($structure, JSON_PRETTY_PRINT));
            
            return response()->json([
                'success' => true,
                'message' => 'Estrutura salva com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar a estrutura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Carregar a estrutura da página
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function load()
    {
        try {
            // Verificar se o arquivo existe
            if (!Storage::exists('page-builder/structure.json')) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'sections' => [],
                        'globalStyles' => [
                            'theme' => 'light',
                            'spacing' => 'normal',
                            'contentWidth' => 'contained'
                        ]
                    ]
                ]);
            }
            
            // Carregar a estrutura do arquivo JSON
            $structure = json_decode(Storage::get('page-builder/structure.json'), true);
            
            return response()->json([
                'success' => true,
                'data' => $structure
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar a estrutura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Renderizar a página com base na estrutura
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(Request $request)
    {
        try {
            $structure = $request->all();
            
            // Aqui você pode implementar a lógica de renderização da página
            // Por exemplo, converter a estrutura em HTML
            
            return response()->json([
                'success' => true,
                'html' => '<div>Página renderizada</div>' // Placeholder
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao renderizar a página: ' . $e->getMessage()
            ], 500);
        }
    }
} 