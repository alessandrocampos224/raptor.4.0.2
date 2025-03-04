<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::factory()->create([
            'name' => 'Listar permissões',
            'slug' => 'permissions.index',
            'description' => 'Listar permissões do sistema'
        ]);
        Permission::factory()->create([
            'name' => 'Criar permissões',
            'slug' => 'permissions.create',
            'description' => 'Criar permissões do sistema'
        ]);
        Permission::factory()->create([
            'name' => 'Editar permissões',
            'slug' => 'permissions.edit',
            'description' => 'Editar permissões do sistema'
        ]);
        Permission::factory()->create([
            'name' => 'Deletar permissões',
            'slug' => 'permissions.destroy',
            'description' => 'Deletar permissões do sistema'
        ]);
        Permission::factory()->create([
            'name' => 'Listar usuários',
            'slug' => 'users.index',
            'description' => 'Listar usuários do sistema'
        ]);
        Permission::factory()->create([
            'name' => 'Criar usuários',
            'slug' => 'users.create',
            'description' => 'Criar usuários do sistema'
        ]);
        Permission::factory()->create([
            'name' => 'Editar usuários',
            'slug' => 'users.edit',
            'description' => 'Editar usuários do sistema'
        ]);
        Permission::factory()->create([
            'name' => 'Deletar usuários',
            'slug' => 'users.destroy',
            'description' => 'Deletar usuários do sistema'
        ]);
        Permission::factory()->create([
            'name' => 'Listar papéis',
            'slug' => 'roles.index',
            'description' => 'Listar papéis do sistema'
        ]);
        Permission::factory()->create([
            'name' => 'Criar papéis',
            'slug' => 'roles.create',
            'description' => 'Criar papéis do sistema'
        ]);
        Permission::factory()->create([
            'name' => 'Editar papéis',
            'slug' => 'roles.edit',
            'description' => 'Editar papéis do sistema'
        ]);
        Permission::factory()->create([
            'name' => 'Deletar papéis',
            'slug' => 'roles.destroy',
            'description' => 'Deletar papéis do sistema'
        ]); 

    }
} 