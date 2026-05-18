<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id('id_comentario');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_producto')->nullable()->constrained('productos', 'id_producto')->nullOnDelete();
            $table->text('comentario');
            $table->integer('calificacion')->nullable();
            $table->timestamps();
        });
    }
};
