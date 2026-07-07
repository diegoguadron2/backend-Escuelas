<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id('id_alumno');
            $table->string('nombre_completo');
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('foto')->nullable();
            $table->enum('genero', ['M', 'F'])->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->unsignedInteger('id_grado')->nullable();
            $table->unsignedInteger('id_seccion')->nullable();
            $table->unsignedBigInteger('id_school');
            $table->timestamps();

            $table->foreign('id_school')
                ->references('id_school')->on('school')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
