<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace App\Http\Controllers;

use App\Core\Support\Form\Fields\AddressInput;
use App\Core\Support\Form\Fields\CalendarInput;
use App\Core\Support\Form\Fields\CheckboxInput;
use App\Core\Support\Form\Fields\CheckboxListInput;
use App\Core\Support\Form\Fields\ColorPickerInput;
use App\Core\Support\Form\Fields\DatePickerInput;
use App\Core\Support\Form\Fields\DateRangeInput;
use App\Core\Support\Form\Fields\FileInput;
use App\Core\Support\Form\Fields\MaskInput;
use App\Core\Support\Form\Fields\PasswordInput;
use App\Core\Support\Form\Fields\RadioInput;
use App\Core\Support\Form\Fields\RichTextInput;
use App\Core\Support\Form\Fields\SelectInput;
use App\Core\Support\Form\Fields\SwitchInput;
use App\Core\Support\Form\Fields\TagsInput;
use App\Core\Support\Form\Fields\TextInput;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Core\Support\Form\Form;
use App\Core\Support\Form\Repeater;
use App\Core\Support\Form\Section;
use App\Core\Support\Table\Table;

use App\Core\Support\Table\Actions\Bulk\DeleteBulkAction;
use App\Core\Support\Table\Actions\EditAction;
use App\Core\Support\Table\Columns\TextColumn;
use App\Core\Support\Table\Filters\SelectFilter;
use App\Http\Requests\User\UserUpdateRequest;
use App\Services\UserService;
use Closure;

class UserController extends AbstractController
{
    protected ?string $model = User::class;
    protected ?string $resource = UserResource::class;
    protected int | string | Closure | null $navigationSort = 3;
    protected string | Closure | null $navigationGroup = 'Configurações';

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }


    protected function form(Form $form): Form
    {
        return $form
            ->sections([
                Section::make('Dados Pessoais')
                    ->columns(4)
                    ->label('Dados Pessoais')
                    ->description('Preencha os dados pessoais do usuário')
                    ->fields([
                        TextInput::make('name')->required()
                            ->columnSpan(2),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->placeholder('E-mail')
                            ->columnSpan(2)
                            ->required(),
                        MaskInput::make('document')
                            ->mask('000.000.000-00')
                            ->tokens([
                                '#' => '\d',
                            ])
                            ->columnSpan(1),
                        MaskInput::make('phone')
                            ->mask('(00) 00000-0000')
                            ->tokens([
                                '#' => '\d',
                            ])
                            ->columnSpan(1),
                        FileInput::make('avatar')
                            ->accept('image/*')
                            ->maxSize(1024)
                            ->multiple()
                            ->columnSpanFull(),
                    ]),

                Section::make('Dados de Acesso')
                    ->label('Dados de Acesso')
                    ->description('Preencha os dados de acesso do usuário')
                    ->fields([
                        PasswordInput::make('password')->required(),
                        PasswordInput::make('password_confirmation')->required(),
                    ]),
                Section::make('Roles')
                    ->label('Responsabilidades')
                    ->description('Selecione as responsabilidades do usuário')
                    ->fields([
                        CheckboxListInput::make('access')
                            ->options(\App\Models\Role::all()->pluck('name', 'id')->toArray())
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Section::make('Endereço')
                    ->columns(1)
                    ->label('Endereço')
                    ->fields([
                        AddressInput::make('default_address')->label('Endereço')->required(),
                    ]),
            ])
            ->columns(2)
            ->action('store', 'update');
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
                EditAction::make()->route('users.edit'),
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
                    ->url(fn($model) => route('users.destroy', $model->id)),
            ]);
    }


    public function update(UserUpdateRequest $request, $id)
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

    public function store(UserUpdateRequest $request)
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
