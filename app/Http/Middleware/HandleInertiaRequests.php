<?php

namespace App\Http\Middleware;

use App\Core\Landlord\Facades\Landlord;
use App\Models\Tenant;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');
 
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'tenant' => Tenant::query()->find(Landlord::getTenantId('tenant_id')),
            'routes' => $this->routes($request),
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
                'warning' => fn() => $request->session()->get('warning'),
                'info' => fn() => $request->session()->get('info'),
            ],
        ];
    }

    /**
     * Carregamento de rotas, dinamicamente.
     * 
     * @param Request $request
     * @return array
     */
    public function routes(Request $request): array
    {
        $allRoutes = [];
        $controllers = glob(app_path('Http/Controllers/*Controller.php'));

        foreach ($controllers as $controller) {
            if ($this->shouldSkipController($controller)) {
                continue;
            }

            $controllerClass = $this->getControllerClass($controller);
            if (class_exists($controllerClass)) {
                $reflection = app($controllerClass);
                $route = [
                    'modelLabel' => $reflection->getModelLabel(),
                    'modelPluralName' => $reflection->getModelLabelPlural(),
                    'navigationGroup' => $reflection->getNavigationGroup(),
                    'navigationSort' => $reflection->getNavigationSort(),
                    'navigationIcon' => $reflection->getNavigationIcon(),
                    'navigationGroupIcon' => method_exists($reflection, 'getNavigationGroupIcon') 
                        ? $reflection->getNavigationGroupIcon() 
                        : null,
                    'navigationGroupSort' => method_exists($reflection, 'getNavigationGroupSort') 
                        ? $reflection->getNavigationGroupSort() 
                        : 0,
                    'slug' => $reflection->getSlug(),
                    'href' => $reflection->getUrlIndex('index', absolute: false),
                    'route' => $reflection->routePrefix('index'),
                    'isActive' => $request->routeIs($reflection->routePrefix('index')),
                ];

                $allRoutes[] = $route;
            }
        } 
        
        return $this->processRoutes($allRoutes);
    }

    protected function shouldSkipController($controller): bool
    {
        return str_contains($controller, 'Auth') || 
               str_contains($controller, 'AbstractController') ||
               str_contains($controller, '/Controller.php');
    }

    protected function getControllerClass($controller): string
    {
        $controller = str_replace(app_path(), '', $controller);
        $controller = str_replace('/', '\\', $controller);
        $controller = str_replace('.php', '', $controller);
        $namespace = str(app()->getNamespace())->beforeLast('\\')->__toString();
        return str($controller)->prepend($namespace)->__toString();
    }

    /**
     * Process and organize routes into grouped and ungrouped categories
     * 
     * @param array $routes
     * @return array
     */
    protected function processRoutes(array $routes): array
    {
        $collection = collect($routes);
        $result = [];
        
        // Split routes into grouped and ungrouped
        $grouped = $collection->filter(function ($route) {
            return !empty($route['navigationGroup']);
        });
        
        $ungrouped = $collection->filter(function ($route) {
            return empty($route['navigationGroup']);
        })->sortBy('navigationSort')->values();
        
        // Process grouped routes
        $groupedRoutes = $grouped->groupBy('navigationGroup')
            ->map(function ($groupRoutes, $groupName) {
                $firstRoute = $groupRoutes->first();
                $items = $groupRoutes->sortBy('navigationSort')->values();
                
                return [
                    'navigationGroup' => $groupName,
                    'isActive' => $items->contains('isActive', true),
                    'navigationSort' => $firstRoute['navigationGroupSort'] ?? 0,
                    'navigationIcon' => $firstRoute['navigationGroupIcon'] ?? null,
                    'items' => $items->toArray(),
                ];
            })
            ->sortBy('navigationSort')
            ->values();
            
        // Add ungrouped routes directly to result
        foreach ($ungrouped as $route) {
            $result[] = $route;
        }
        
        // Add grouped routes to result
        foreach ($groupedRoutes as $group) {
            $result[] = $group;
        }
        
        return collect($result)
        ->sortBy('navigationSort')
        ->values()->toArray();
    }
}
