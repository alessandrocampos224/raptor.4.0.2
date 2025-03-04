<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Console\Commands;

use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Enums\TenantStatus;

class CreateTenantCommand extends Command
{
    protected $signature = 'tenant:create 
                            {name? : Nome do tenant}
                            {email? : Email do usuário admin}
                            {--domain= : Domínio do tenant}
                            {--database= : Nome do banco de dados}';

    protected $description = 'Cria um novo tenant com usuário administrador';

    public function handle(TenantService $service)
    {
        $name = $this->argument('name') ?? $this->ask('Qual o nome do tenant?');
        $email = $this->argument('email') ?? $this->ask('Qual o email do usuário admin?');
        $domain = $this->option('domain') ?? $this->ask('Qual o domínio do tenant?');
        $database = $this->option('database') ?? strtolower(str_replace(' ', '_', $name));
        
        $password = $this->secret('Qual a senha do usuário admin?');

        $tenant = $service->create([
            'name' => $name,
            'domain' => $domain,
            'database' => $database,
            'email' => $email,
            'status' => TenantStatus::PUBLISHED
        ]);

        $user = User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $tenant->users()->attach($user->id);

        // Criar role admin se não existir
        $role = $tenant->roles()->firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrador',
                'description' => 'Administrador do sistema',
                'special' => true
            ]
        );

        $user->roles()->attach($role->id);

        $this->info('Tenant criado com sucesso!');
        $this->table(
            ['Tenant', 'Domínio', 'Email'],
            [[$tenant->name, $tenant->domain, $user->email]]
        );

        return Command::SUCCESS;
    }
}
