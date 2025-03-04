<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $tenant = null;
        
        // Verifica se é um domínio principal
        $tenant = Tenant::where('domain', $host)
            ->where('status', 'published')
            ->where('is_primary', true)
            ->first();

        if (!$tenant) {
            // Extrai o subdomínio
            $parts = explode('.', $host);
            if (count($parts) > 2) {
                $prefix = $parts[0];
                // Procura tenant pelo prefix
                $tenant = Tenant::where('prefix', $prefix)
                    ->where('status', 'published')
                    ->first();
            }
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        session()->put('tenant_id', $tenant->id);
        app()->singleton('tenant', fn() => $tenant);
        
        return $next($request);
    }
}
