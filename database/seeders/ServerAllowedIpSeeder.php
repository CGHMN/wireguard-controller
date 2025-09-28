<?php

namespace Database\Seeders;

use App\Models\Server;
use App\Models\ServerAllowedIp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServerAllowedIpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$server = Server::firstOrFail();

		foreach(['10.1.0.0/24', '10.2.0.0/24', '10.3.0.0/24'] as $cidr) {
			ServerAllowedIp::create([
				'cidr' => $cidr,
				'server_id' => $server->id
			]);
		}
    }
}
