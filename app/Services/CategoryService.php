<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;

/**
 * Class CategoryService
 * 
 * Serviço responsável pela lógica de negócios relacionada ao modelo Category.
 * Implementa operações CRUD e regras de negócio específicas do domínio.
 * Herda funcionalidades comuns do AbstractService.
 */
class CategoryService extends AbstractService
{
    /**
     * Inicializa o serviço com injeção de dependência do modelo e da requisição
     * 
     * @param Category $model Instância do modelo
     * @param Request $request Requisição HTTP atual
     */
    public function __construct(Category $model, Request $request)
    {
        parent::__construct($model, $request);
    }
    
    /**
     * Você pode adicionar métodos personalizados aqui para implementar
     * lógicas de negócio específicas para o modelo Category, como:
     * 
     * - Processamento de dados complexos
     * - Integrações com serviços externos
     * - Validações específicas de domínio
     * - Operações em lote
     * - Relatórios ou estatísticas
     */
}
