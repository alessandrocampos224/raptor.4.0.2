<?php

/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */

namespace App\Http\Controllers;

use Closure;

class DashboardController extends AbstractController
{
    protected string | Closure | null $slug = 'dashboard';
    protected string | Closure | null $modelLabelPlural = "Dashboard";
    protected string | Closure | null $modelLabel = 'Dashboard';

    public function __invoke()
    {
        return inertia('Dashboard');
    }
}
