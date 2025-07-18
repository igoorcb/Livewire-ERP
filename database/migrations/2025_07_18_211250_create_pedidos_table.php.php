<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('cliente');
            $table->string('email');
            $table->string('cep');
            $table->string('endereco');
            $table->string('status')->default('novo');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('frete', 10, 2);
            $table->decimal('total', 10, 2);
            $table->foreignId('cupom_id')->nullable()->constrained('cupons');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
};
