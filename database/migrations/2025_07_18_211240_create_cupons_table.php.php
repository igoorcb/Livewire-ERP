<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cupons', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->date('validade');
            $table->decimal('valor_minimo', 10, 2);
            $table->decimal('desconto', 10, 2);
            $table->enum('tipo', ['percentual', 'fixo']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cupons');
    }
};
