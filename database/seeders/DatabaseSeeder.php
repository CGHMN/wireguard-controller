<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Env;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		if (Env::get('APP_ENV') == 'local') {
			$this->call(ServerAllowedIpSeeder::class);
			$this->call(PeerSeeder::class);
			$this->call(PeerAllowedIpSeeder::class);
		} else if (is_file(__DIR__.'/ProductionServerSeeder.php')) {
			$this->call(ProductionServerSeeder::class);
		}
	}
}
