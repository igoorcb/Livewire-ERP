<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('estoques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->foreignId('variacao_id')->nullable()->constrained('variacoes')->onDelete('cascade');
            $table->integer('quantidade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('estoques');
    }
};
