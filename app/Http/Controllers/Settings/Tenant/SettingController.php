<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace App\Http\Controllers\Settings\Tenant;

use App\Core\Support\Form\Fields\AddressInput;
use App\Core\Support\Form\Fields\DecimalInput;
use App\Core\Support\Form\Fields\DocumentInput;
use App\Core\Support\Form\Fields\MaskInput;
use App\Core\Support\Form\Fields\PhoneInput;
use App\Core\Support\Form\Fields\RadioInput;
use App\Core\Support\Form\Fields\TextAreaInput;
use App\Core\Support\Form\Fields\TextInput;
use App\Core\Support\Form\Form;
use App\Core\Support\Form\Section;
use App\Http\Controllers\AbstractController;
use App\Http\Requests\Tenant\Settings\UpdateRequest;
use App\Http\Resources\TenantResource;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends AbstractController
{
    protected ?string $model = Tenant::class;
    protected ?string $resource = TenantResource::class;

    public function __construct(\App\Services\Tenant\SettingService $service)
    {
        $this->service = $service;
    }

    protected function form(Form $form): Form
    {
        return $form
            ->sections([
                Section::make('Dados')
                    ->columns(12)
                    ->label('Dados')
                    ->fields([
                        TextInput::make('name')->label('Nome')->required()->columnSpan(7),
                        TextInput::make('email')->label('E-mail')->required()->email()->columnSpan(5),
                        DocumentInput::make('document')->label('CNPJ')->required()->cnpj()->columnSpan(4),
                        PhoneInput::make('phone')->label('Telefone')->required()->columnSpan(4), 
                        RadioInput::make('status')->label('Situação')->options([
                            'draft' => 'Rascunho',
                            'published' => 'Publicado',
                        ])->required()->columnSpanFull(),
                        TextAreaInput::make('description')->label('Descrição')->columnSpanFull(),
                    ]),
                Section::make('Endereço')
                    ->columns(1)
                    ->label('Endereço')
                    ->fields([
                        AddressInput::make('default_address')->label('Endereço')->required(),
                    ]),
            ]);
    }

    public function index(Request $request)
    {
        $record = $this->service->currentTenant();
        return Inertia::render('settings/tenant/Setting', $this->form(Form::make($request)
            ->action(route('settings.tenant.setting.store'))
            ->route('settings.tenant.setting.store')
            ->model($this->model)
            ->record($record))->toArray());
    }

    public function store(UpdateRequest $request)
    {
        try {
            $record = $this->service->currentTenant(); 
            $this->service->update($record, $request->validated());
            return redirect()->route('settings.tenant.setting.index')->with('success', 'Registro atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar o registro: ' . $e->getMessage());
        }
    }
}
