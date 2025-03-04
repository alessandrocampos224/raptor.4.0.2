<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace App\Services\Tenant;

use App\Core\Landlord\Facades\Landlord;
use App\Models\Tenant;
use App\Services\AbstractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingService extends AbstractService
{

    public function __construct(Tenant $model, Request $request)
    {
        parent::__construct($model, $request);
    }

    public function currentTenant(): Tenant
    {
        return Cache::remember('currentTenant', 60, function () {
            return Tenant::query()->find(Landlord::getTenantId('tenant_id'));
        });
    }

    protected function afterUpdate($model, array $data): void
    { 
        if($default_address = data_get($data, 'default_address')) { 
            $model->defaultAddress()->updateOrCreate(['id' => $default_address['id'] ?? null], $default_address);
        }

        Cache::forget('currentTenant');
    }
}
