<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Services;

use App\Models\Page;
use Illuminate\Http\Request;

/**
 * Class PageService
 * 
 * Serviço responsável pela lógica de negócios relacionada ao modelo Page.
 * Implementa operações CRUD e regras de negócio específicas do domínio.
 * Herda funcionalidades comuns do AbstractService.
 */
class PageService extends AbstractService
{
    /**
     * Inicializa o serviço com injeção de dependência do modelo e da requisição
     * 
     * @param Page $model Instância do modelo
     * @param Request $request Requisição HTTP atual
     */
    public function __construct(Page $model, Request $request)
    {
        parent::__construct($model, $request);
    }
    
    /**
     * Você pode adicionar métodos personalizados aqui para implementar
     * lógicas de negócio específicas para o modelo Page, como:
     * 
     * - Processamento de dados complexos
     * - Integrações com serviços externos
     * - Validações específicas de domínio
     * - Operações em lote
     * - Relatórios ou estatísticas
     */
}
