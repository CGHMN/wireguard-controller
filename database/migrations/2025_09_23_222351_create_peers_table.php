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
        Schema::create('peers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->string('name')->unique();
			$table->string('public_key')->unique();
			$table->string('tunnel_ip')->unqiue();
			$table->string('preshared_key')->nullable();

			$table->foreignId('user_id')
				->nullable()
				->cascadeOnUpdate()
				->restrictOnDelete();

			$table->foreignId('server_id')
				->cascadeOnUpdate()
				->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peers');
    }
};
