<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TenantController extends Controller
{
    protected $service;

    public function __construct(TenantService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return Inertia::render('crud/Index', [
            'tenants' => Tenant::query()
                ->latest()
                ->paginate(10)
                ->through(fn ($tenant) => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'domain' => $tenant->domain,
                    'status' => $tenant->status,
                    'created_at' => $tenant->created_at->format('d/m/Y')
                ])
        ]);
    }

    public function create()
    {
        return Inertia::render('crud/Create');
    }

    public function store(Request $request)
    {
        $tenant = $this->service->create($request->validated());
        
        return redirect()->route('admin.tenants.index')
            ->with('message', 'Tenant criado com sucesso!');
    }

    public function edit(Tenant $tenant)
    {
        return Inertia::render('crud/Edit', [
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'domain' => $tenant->domain,
                'email' => $tenant->email,
                'phone' => $tenant->phone,
                'status' => $tenant->status,
            ]
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $this->service->update($tenant, $request->validated());
        
        return redirect()->route('admin.tenants.index')
            ->with('message', 'Tenant atualizado com sucesso!');
    }

    public function destroy(Tenant $tenant)
    {
        $this->service->delete($tenant);
        
        return redirect()->route('admin.tenants.index')
            ->with('message', 'Tenant removido com sucesso!');
    }
}
