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
        Schema::create('server_allowed_ips', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->string('cidr')->unique();

			$table->foreignId('server_id')
				->cascadeOnUpdate()
				->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_allowed_ips');
    }
};
