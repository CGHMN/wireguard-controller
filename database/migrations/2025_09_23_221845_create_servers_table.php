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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

			$table->string('name')->unique();
			$table->string('interface_name')->unique();
			$table->string('private_key')->unique();
			$table->string('public_key')->unique();
			$table->string('endpoint');
			$table->integer('listen_port')->unique();
			$table->integer('mtu')->default(1320);

			$table->string('tunnel_ip')->unique();
			$table->string('routed_subnet')->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
