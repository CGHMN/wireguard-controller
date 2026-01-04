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

		foreach(['192.0.2.1/24', '240.0.0.0/16'] as $cidr) {
			ServerAllowedIp::create([
				'cidr' => $cidr,
				'server_id' => $server->id
			]);
		}
    }
}
