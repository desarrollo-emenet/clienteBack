<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('client_metadata', function (Blueprint $table) {
        $table->id();

        // Relación con usuario
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->string('numero_cliente');
        $table->json('metadata')->nullable();
        $table->timestamp('last_updated_at')->nullable(); // para TTL
        $table->timestamps();
        $table->unique(['user_id', 'numero_cliente']);

        // Índice
        $table->index('numero_cliente');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('client_metadata');
    }
};
