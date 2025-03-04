<?php
/**
 * Created by Claudio Campos.
 * User: callcocam@gmail.com, contato@sigasmart.com.br
 * https://www.sigasmart.com.br
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $name = config('shinobi.tables.permission_role');

        Schema::create($name, function (Blueprint $table) {
            $table->foreignUlid('permission_id')->constrained()->onDelete('cascade');
            $table->foreignUlid('role_id')->constrained()->onDelete('cascade');
            $table->primary(['permission_id', 'role_id']);
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('permission_role');
    }
};
