<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school', function (Blueprint $table) {
            $table->id('id_school');
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('email')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->unsignedBigInteger('id_user');
            $table->timestamps();

            $table->foreign('id_user')
                ->references('id_user')->on('usuarios')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school');
    }
};
