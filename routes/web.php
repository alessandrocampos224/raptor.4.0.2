<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboards', function () {
    return  redirect()->route('dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth' ])
// ->prefix('admin')->name('admin.')
->group(function () {
    
    Route::post('bulk-action', \App\Http\Controllers\Admin\BulkActionController::class)->name('bulk-action');
 
    Route::get('configuracoes', [\App\Http\Controllers\Settings\Tenant\SettingController::class, 'index'])->name('settings.tenant.setting.index');
    Route::post('configuracoes', [\App\Http\Controllers\Settings\Tenant\SettingController::class, 'store'])->name('settings.tenant.setting.store');

    Route::resource('tenants', TenantController::class);
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    // Outras rotas admin aqui
    route::resource('pages', \App\Http\Controllers\PageController::class);
    // Rota específica para salvar dados do PageBuilder
    Route::post('pages/{page}/save-builder', [\App\Http\Controllers\PageController::class, 'saveBuilder'])->name('pages.save-builder');
    route::resource('posts', \App\Http\Controllers\PostController::class);
    route::resource('categories', \App\Http\Controllers\CategoryController::class);
    
    Route::resource('sobre-nos', \App\Http\Controllers\AboutController::class)
    ->names('abouts')
    ->parameters(['sobre-nos' => 'about']);

    Route::get('dashboard', \App\Http\Controllers\DashboardController::class)->name('dashboard.index');
});

// Rotas para o PageBuilder - acessíveis sem autenticação
Route::get('api/page-builder/posts', [\App\Http\Controllers\PostController::class, 'getPostsForPageBuilder'])->name('api.page-builder.posts');
Route::get('api/page-builder/categories', [\App\Http\Controllers\CategoryController::class, 'getCategoriesForPageBuilder'])->name('api.page-builder.categories');
Route::get('api/page-builder/categories/{id}/posts', [\App\Http\Controllers\CategoryController::class, 'getCategoryPostsForPageBuilder'])->name('api.page-builder.category.posts');

// Rota pública para obter dados de uma página pelo slug
Route::get('api/pages/{slug}', [\App\Http\Controllers\PageController::class, 'getPageBySlug'])->name('api.pages.slug');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Rotas dinâmicas para páginas internas (deve ser a última rota)
Route::get('{path}', [\App\Http\Controllers\Internal\PageController::class, 'show'])
    ->where('path', '(?!login|register).*') // Exclui login e register do catch-all
    ->name('internal.page');