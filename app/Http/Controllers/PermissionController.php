<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace App\Http\Controllers;

use App\Core\Support\Form\Fields\RadioInput;
use App\Core\Support\Form\Fields\TextAreaInput;
use App\Core\Support\Form\Fields\TextInput;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Core\Support\Table\Table;
use App\Core\Support\Form\Form;
use App\Core\Support\Form\Section;
use App\Core\Support\Table\Actions\Bulk\DeleteBulkAction;
use App\Core\Support\Table\Actions\EditAction;
use App\Support\Core\Table\Actions\DeleteAction;
use App\Core\Support\Table\Columns\TextColumn;
use App\Core\Support\Table\Filters\SelectFilter;
use App\Http\Requests\Permission\UpdateRequest;
use App\Http\Requests\Permission\StoreRequest;
use App\Services\PermissionService;
use Closure;

class PermissionController extends AbstractController
{
    protected ?string $model = Permission::class;
    protected ?string $resource = PermissionResource::class;
    protected int | string | Closure | null $navigationSort = 1;
    protected string | Closure | null $navigationGroup = 'Configurações';

    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }

    protected function form(Form $form): Form
    {
        return $form
            ->sections([
                Section::make('Dados')
                    ->columns(1)
                    ->label('Dados')
                    ->fields([
                        TextInput::make('name')->label('Nome')->required(), 
                        TextInput::make('slug')->label('Slug')->required(),
                        RadioInput::make('status')->label('Situação')->options([
                            'draft' => 'Rascunho',
                            'published' => 'Publicado',
                        ])->required(),
                        TextAreaInput::make('description')->label('Descrição'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('status')->sortable()->searchable(),
                TextColumn::make('created_at')->format(fn($value) => $value->diffForHumans()),
                TextColumn::make('updated_at')->format(fn($value) => $value->diffForHumans()),
            ])
            ->actions([
                EditAction::make()->route('permissions.edit'), 
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
                    ->url(fn($model) => route('permissions.destroy', $model->id)),
            ]);
    }


    public function update(UpdateRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $model = $this->getModel()::findOrFail($id);
            $this->service->update($model, $validated);
            return redirect()->route($this->routePrefix('index'))->with('success', 'Registro atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }


    public function store(UpdateRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->service->create($validated);
            return redirect()->route($this->routePrefix('index'))->with('success', 'Registro criado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
