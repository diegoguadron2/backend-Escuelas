// database/migrations/xxxx_modify_foto_field_to_text.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('school', function (Blueprint $table) {
            $table->text('foto')->nullable()->change();
        });

        Schema::table('alumnos', function (Blueprint $table) {
            $table->text('foto')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('school', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->change();
        });

        Schema::table('alumnos', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->change();
        });
    }
};