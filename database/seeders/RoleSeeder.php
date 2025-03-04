<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {

            Role::create([
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrador do sistema',
                'special' => true,
                'status' => 'published'
            ]);

            Role::create([
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Usuário do sistema',
                'special' => false,
                'status' => 'published'
            ]);
        }
    }
}
