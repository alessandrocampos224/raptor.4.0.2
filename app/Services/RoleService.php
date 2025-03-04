<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleService extends AbstractService
{
     public function __construct(Role $model, Request $request)
    {
        parent::__construct($model, $request);
    }

    protected function afterStore(array $data): void
    {
        $this->model->permissions()->sync(data_get($data, 'access', []));
    }
    protected function afterUpdate($model, array $data): void
    {        
        $model->permissions()->sync(data_get($data, 'access', []));
    }

    public function getPermissionsOptions(): array
    {
        return Permission::query()->pluck('name', 'id')->toArray();
    }
}
