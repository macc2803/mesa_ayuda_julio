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
        Schema::create('peticions', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('estado', ['enviado', 'proceso', 'completado'])->default('enviado');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->foreignId('area_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peticions');
    }
};
