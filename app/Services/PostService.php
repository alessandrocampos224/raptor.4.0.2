<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Services;

use App\Models\Post;
use Illuminate\Http\Request;

/**
 * Class PostService
 * 
 * Serviço responsável pela lógica de negócios relacionada ao modelo Post.
 * Implementa operações CRUD e regras de negócio específicas do domínio.
 * Herda funcionalidades comuns do AbstractService.
 */
class PostService extends AbstractService
{
    /**
     * Inicializa o serviço com injeção de dependência do modelo e da requisição
     * 
     * @param Post $model Instância do modelo
     * @param Request $request Requisição HTTP atual
     */
    public function __construct(Post $model, Request $request)
    {
        parent::__construct($model, $request);
    }
    
    /**
     * Você pode adicionar métodos personalizados aqui para implementar
     * lógicas de negócio específicas para o modelo Post, como:
     * 
     * - Processamento de dados complexos
     * - Integrações com serviços externos
     * - Validações específicas de domínio
     * - Operações em lote
     * - Relatórios ou estatísticas
     */
}
