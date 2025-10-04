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
		Schema::table('peer_allowed_ips', function (Blueprint $table) {
			$table->foreign('peer_id')
				->references('id')
				->on('peers')
				->cascadeOnUpdate()
				->cascadeOnDelete();

			$table->integer('peer_id')->nullabe()->change();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		//
	}
};
