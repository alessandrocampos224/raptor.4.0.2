<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PageStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');//nome da página
            $table->string('slug')->index();//slug da página
            $table->string('title');//titulo da página
            $table->text('description')->nullable();//descrição da página
            $table->json('config')->nullable()->comment('JSON com componentes, estilos globais e metadados');//configurações da página
            $table->string('tipo')->nullable()->comment('Tipo de template: content, form, list, etc');//tipo de template da página
            $table->timestamp('published_at')->nullable();//data de publicação da página
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();//usuario criador da página
            $table->foreignUlid('updated_by')->nullable()->constrained('users')->nullOnDelete();//usuario atualizador da página
            $table->string('seo_title')->nullable();//titulo da página para o seo
            $table->text('seo_keywords')->nullable();//palavras chaves para o seo
            $table->boolean('is_homepage')->nullable()->default(false);//se a página é a página inicial
            $table->integer('order')->nullable()->default(0)->comment('Ordem de exibição');//ordem de exibição da página
            $table->string('parent_slug')->nullable()->comment('Slug da página pai');
            $table->enum('status', array_column(PageStatus::cases(), 'value'))->nullable()
                ->default(PageStatus::DRAFT->value);//status da página
            $table->timestamps();//data de criação e atualização da página
            $table->softDeletes();//data de exclusão da página
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
}; 