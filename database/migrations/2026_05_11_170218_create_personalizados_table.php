<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personalizado', function (Blueprint $table) {

            $table->id('ID_personalizado');

            $table->unsignedInteger('ID_usu');

            $table->unsignedInteger('ID_prod');

            $table->longText('json_diseno');

            $table->longText('imagen_preview');

            $table->timestamp('fecha_creacion')
                ->useCurrent();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personalizado');
    }
};