<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
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
use App\Http\Requests\Category\UpdateRequest;
use App\Http\Requests\Category\StoreRequest;
use App\Services\CategoryService;

/**
 * Class CategoryController
 * 
 * Controller responsável por gerenciar operações CRUD para o modelo Category.
 * Herda funcionalidades do AbstractController para automatizar tarefas comuns.
 */
class CategoryController extends AbstractController
{
    /**
     * Define o modelo que será usado pelo controller
     * 
     * @var string|null
     */
    protected ?string $model = Category::class;
    
    /**
     * Define o recurso usado para transformação de dados em APIs
     * 
     * @var string|null
     */
    protected ?string $resource = CategoryResource::class;

    /**
     * Inicializa o controller com injeção de dependência do serviço
     * 
     * @param CategoryService $service Serviço que contém a lógica de negócios
     */
    public function __construct(CategoryService $service)
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
                    ->label('Dados')
                    ->fields([
                        TextInput::make('name')->label('Nome')->required(), 
                        RadioInput::make('status')->label('Situação')
                            ->options([
                                'draft' => 'Rascunho',
                                'published' => 'Publicado',
                            ])->required()->columnSpanFull(),
                        TextAreaInput::make('description')->label('Descrição'),
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
                EditAction::make()->route('categories.edit'),
                //DeleteAction::make()->route('categories.destroy'),
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
                    ->url(fn($model) => route('categories.destroy', $model->id)),
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
            $validated = $request->validated();
            $model = $this->getModel()::findOrFail($id);
            if ($this->service->update($model, $validated)) {
                return redirect()->route($this->routePrefix('index'))->with('success', 'Registro atualizado com sucesso!');
            }
            return redirect()->back()->withErrors('Erro ao atualizar o registro')->with('error', $this->service->getError());
        } catch (\Exception $e) {
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
     * Retorna uma lista de categorias para uso no construtor de páginas
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoriesForPageBuilder()
    {
        try {
            $categories = Category::where('status', 'published')
                ->select('id', 'name', 'slug', 'created_at')
                ->withCount('posts')
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => CategoryResource::collection($categories)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Retorna os posts de uma categoria específica para uso no construtor de páginas
     * 
     * @param string $id ID da categoria
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryPostsForPageBuilder($id)
    {
        try {
            $category = Category::findOrFail($id);
            $posts = $category->posts()
                ->where('status', 'published')
                ->select('id', 'name', 'description', 'category_id', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => \App\Http\Resources\PostResource::collection($posts)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}