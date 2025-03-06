<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Http\Controllers;

use App\Http\Resources\PageResource;
use App\Models\Page;
use App\Core\Support\Table\Table;
use App\Core\Support\Form\Form;

use App\Core\Support\Table\Actions\Bulk\DeleteBulkAction;
use App\Core\Support\Table\Actions\EditAction;
use App\Support\Core\Table\Actions\DeleteAction;
use App\Core\Support\Table\Columns\TextColumn; 
use App\Core\Support\Table\Filters\SelectFilter;
use App\Core\Support\Form\Fields\TextAreaInput;
use App\Core\Support\Form\Fields\RadioInput;
use App\Core\Support\Form\Fields\TextInput;
use App\Core\Support\Form\Section;
use App\Http\Requests\Page\UpdateRequest;
use App\Http\Requests\Page\StoreRequest;
use App\Services\PageService;
use App\Core\Support\Table\Actions\Action;
use Illuminate\Http\Request;
use Inertia\Inertia;
/**
 * Class PageController
 * 
 * Controller responsável por gerenciar operações CRUD para o modelo Page.
 * Herda funcionalidades do AbstractController para automatizar tarefas comuns.
 */
class PageController extends AbstractController
{
    /**
     * Define o modelo que será usado pelo controller
     * 
     * @var string|null
     */
    protected ?string $model = Page::class;
    
    /**
     * Define o recurso usado para transformação de dados em APIs
     * 
     * @var string|null
     */
    protected ?string $resource = PageResource::class;

    /**
     * Inicializa o controller com injeção de dependência do serviço
     * 
     * @param PageService $service Serviço que contém a lógica de negócios
     */
    public function __construct(PageService $service)
    {
        $this->service = $service;
    }

    /**
     * Define o formulário de criação/edição do modelo
     * 
     * Este método configura os campos, validações e organizações do formulário
     * usado nas operações de criação (create) e edição (edit) dos registros.
     * 
     * @param Form $form Instância do construtor de formulário
     * @return Form Formulário configurado
     */
    protected function form(Form $form): Form
    {
        return $form
            ->sections([
                Section::make('Dados') 
                    ->label('Dados')->columns(12)
                    ->fields([
                        TextInput::make('name')->label('Nome')->required()->columnSpan(6),
                         TextInput::make('slug')->label('Slug')->required()->columnSpan(6),
                         TextInput::make('title')->label('Título')->required()->columnSpan(6),
                         RadioInput::make('tipo')->label('Tipo')
                         ->options([
                            'content' => 'Conteúdo',
                            'form' => 'Formulário',
                            'list' => 'Lista',
                         ])->required()->columnSpan(12),
                        RadioInput::make('status')->label('Situação')
                            ->options([
                                'draft' => 'Rascunho',
                                'published' => 'Publicado',
                            ])->required()->columnSpanFull(),
                        TextAreaInput::make('description')->label('Descrição')->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * Define a tabela para listagem dos registros
     * 
     * Este método configura as colunas, ações, filtros e ações em lote
     * disponíveis na visualização de lista (index) do CRUD.
     * 
     * @param Table $table Instância do construtor de tabela
     * @return Table Tabela configurada
     */
    protected function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('status')->sortable()->searchable(),
                TextColumn::make('created_at')->format(fn($value) => $value->diffForHumans()),
                TextColumn::make('updated_at')->format(fn($value) => $value->diffForHumans()),
            ])
            ->actions([
                EditAction::make()->route('pages.edit'),
                Action::make('builder')->route('pages.show')->icon('Eye'),
                //DeleteAction::make()->route('pages.destroy'),
            ])
            ->filters([
                SelectFilter::make('status', 'Situação')
                    ->options([
                        'draft' => 'Rascunho',
                        'published' => 'Publicado',
                    ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make('Excluir Selecionados')
                    ->requireConfirmation(true)
                    ->url(fn($model) => route('pages.destroy', $model->id)),
            ]);
    }

    /**
     * Atualiza um registro existente no banco de dados
     * 
     * Este método processa a requisição de atualização, valida os dados
     * e utiliza o serviço para persistir as alterações.
     * 
     * @param UpdateRequest $request Requisição com dados validados
     * @param mixed $id Identificador único do registro
     * @return \Illuminate\Http\RedirectResponse Redirecionamento com mensagem de sucesso ou erro
     */
    public function update(UpdateRequest $request, $id)
    {
        try {
            // Log para depuração
            \Log::info('Dados recebidos no update:', [
                'request' => $request->all(),
                'validated' => $request->validated(),
                'id' => $id
            ]);
            
            $validated = $request->validated();
            $model = $this->getModel()::findOrFail($id);
            
            if ($this->service->update($model, $validated)) {
                // Se for uma requisição AJAX, retorna JSON
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Página atualizada com sucesso!',
                        'data' => $model
                    ]);
                }
                
                // Caso contrário, redireciona com mensagem de sucesso
                return redirect()->route($this->routePrefix('index'))->with('success', 'Registro atualizado com sucesso!');
            }
            
            // Se for uma requisição AJAX, retorna JSON com erro
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $this->service->getError() ?: 'Erro ao atualizar a página'
                ], 422);
            }
            
            return redirect()->back()->withErrors('Erro ao atualizar o registro')->with('error', $this->service->getError());
        } catch (\Exception $e) {
            // Log para depuração
            \Log::error('Erro no update:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Se for uma requisição AJAX, retorna JSON com erro
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Cria um novo registro no banco de dados
     * 
     * Este método processa a requisição de criação, valida os dados
     * e utiliza o serviço para persistir o novo registro.
     * 
     * @param StoreRequest $request Requisição com dados validados
     * @return \Illuminate\Http\RedirectResponse Redirecionamento com mensagem de sucesso ou erro
     */
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            if ($this->service->create(array_merge($validated, ['user_id' => auth()->user()->id]))) {
                return redirect()->route($this->routePrefix('index'))->with('success', 'Registro criado com sucesso!');
            }
            return redirect()->back()->withErrors('Erro ao criar o registro')->with('error', $this->service->getError());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
    
    /**
     * Retorna os dados de uma página pelo slug para uso no frontend
     * 
     * @param string $slug Slug da página
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPageBySlug($slug)
    {
        try {
            $page = Page::where('slug', $slug)->first();
            
            if (!$page) {
                return response()->json([
                    'success' => false,
                    'message' => 'Página não encontrada'
                ], 404);
            }
            
            // Obter componentes e estilos globais do campo config
            $config = $page->config ?? [];
            $components = $config['components'] ?? [];
            $globalStyles = $config['globalStyles'] ?? [
                'theme' => 'light',
                'spacing' => 'normal',
                'contentWidth' => 'contained'
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $page->id,
                    'title' => $page->title ?? $page->name,
                    'slug' => $page->slug,
                    'components' => $components,
                    'globalStyles' => $globalStyles
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    protected function defaults(): array
    {
        return [
            'name' => '',
            'slug' => '',
            'title' => '',
            'tipo' => 'content',
            'status' => 'published',
            'description' => '',
            'config' => [],
        ];
    }
    protected function getViewShow()
    {
        return  'page-builder/Show';
    }

    /**
     * Sobrescreve o método show do AbstractController para passar as propriedades necessárias
     * para o componente Show.vue
     */
    public function show(Request $request, $id)
    {
        $model = $this->getModel()::findOrFail($id);
        
        // Log para depuração
        \Log::info('Dados da página:', [
            'id' => $id,
            'config' => $model->config,
            'components' => $model->config['components'] ?? []
        ]);
        
        // Componentes da página
        $components = $model->config['components'] ?? [];
        
        // Log dos componentes que serão passados para o PageBuilder
        \Log::info('Componentes que serão passados para o PageBuilder:', [
            'components' => $components
        ]);
        
        return Inertia::render($this->getViewShow(), [
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard.index')],
                ['title' => $this->getModelLabelPlural(), 'href' => route($this->routePrefix('index'))],
                ['title' => $model->name, 'href' => ''],
            ],
            'sections' => $components,
            'record' => $model,
            'config' => [
                'action' => route($this->routePrefix('update'), $model),
                'method' => 'put',
                'fullWidth' => true,
            ],
            'actions' => [],
            'model' => $model,
            'modelLabel' => $this->getModelLabel(),
            'modelLabelPlural' => $this->getModelLabelPlural(),
            'modelDescription' => $this->getModelDescription(),
            'navigationSort' => $this->getNavigationSort(),
            'navigationIcon' => $this->getNavigationIcon(),
        ]);
    }

    /**
     * Salva os dados do PageBuilder
     * 
     * @param Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveBuilder(Request $request, $id)
    {
        try {
            // Log para depuração
            \Log::info('Dados recebidos no saveBuilder:', [
                'request' => $request->all(),
                'id' => $id
            ]);
            
            $model = $this->getModel()::findOrFail($id);
            
            // Log do estado atual do modelo
            \Log::info('Estado atual do modelo antes da atualização:', [
                'id' => $model->id,
                'config' => $model->config
            ]);
            
            // Validar os dados
            $validated = $request->validate([
                'config' => 'required|array',
                'config.components' => 'present|array',
                'config.globalStyles' => 'required|array',
            ]);
            
            // Garante que components é um array mesmo que vazio
            if (!isset($validated['config']['components'])) {
                $validated['config']['components'] = [];
            }
            
            // Log dos dados validados
            \Log::info('Dados validados para salvar:', [
                'validated' => $validated
            ]);
            
            // Atualizar diretamente o campo config para garantir que os componentes sejam salvos
            $model->config = $validated['config'];
            $model->save();
            
            // Log do estado após a atualização
            $updatedModel = $this->getModel()::findOrFail($id);
            \Log::info('Estado do modelo após a atualização direta:', [
                'id' => $updatedModel->id,
                'config' => $updatedModel->config
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Página atualizada com sucesso!',
                'data' => $updatedModel
            ]);
        } catch (\Exception $e) {
            // Log para depuração
            \Log::error('Erro no saveBuilder:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}