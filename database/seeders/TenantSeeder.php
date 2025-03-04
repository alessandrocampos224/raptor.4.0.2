<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::factory()->create(
            [
                'name' => 'SIGA SMART',
                'email' => 'contato@sigasmart.com.br',
                'domain' => request()->getHost(),
                'status' => 'published',
                'description' => 'Sistema de Gestão para Empresas',
                'phone' => '(11) 99999-9999',
                'document' => '12345678901',
                'settings' => [
                    'logo' => 'https://www.sigasmart.com.br/img/logo.png',
                    'favicon' => 'https://www.sigasmart.com.br/img/favicon.png',
                    'color' => '#000000'
                ],
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}
