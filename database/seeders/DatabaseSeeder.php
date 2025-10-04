<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		//$this->call(UserSeeder::class);

		if (is_file(__DIR__.'/ProductionServerSeeder.php')) {
			$this->call(ProductionServerSeeder::class);
		}

		//$this->call(ServerAllowedIpSeeder::class);

		//$this->call(PeerSeeder::class);
		//$this->call(PeerAllowedIpSeeder::class);
	}
}
