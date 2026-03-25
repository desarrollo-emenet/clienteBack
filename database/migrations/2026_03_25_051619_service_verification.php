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
        //
         Schema::create('service_verifications', function (Blueprint $table) {
            $table->id();            
            $table->string('numero_cliente');
            $table->string('codigo', 6);
            $table->timestamp('expires_at');
            $table->unsignedTinyInteger('intentos')->default(0);
            $table->timestamps();
            $table->index(['numero_cliente', 'codigo']);

            // FK al usuario
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
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
        Schema::dropIfExists('service_verifications');
    }
};
