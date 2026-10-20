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
        Schema::create('user_devices_token', function (Blueprint $table) {
            $table->id();
            $table->string('fcm_token', 500)->unique();
            $table->string('device_type')->nullable();
            $table->timestamps();

            // Llave foránea a los usuarios
            $table->foreignId('user_id')->constrained('users_client_app')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_devices_token');
    }
};
