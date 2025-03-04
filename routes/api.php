// Rotas para o Page Builder
Route::prefix('page-builder')->group(function () {
    // Rotas para categorias
    Route::get('/categories', [CategoryController::class, 'getCategoriesForPageBuilder']);
    Route::get('/categories/{id}/posts', [CategoryController::class, 'getCategoryPostsForPageBuilder']);
    
    // Rota para posts
    Route::get('/posts', [PostController::class, 'getPostsForPageBuilder']);
}); 