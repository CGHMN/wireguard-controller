<?php

namespace Database\Seeders;

use App\Models\Server;
use App\Models\ServerAllowedIp;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$server = Server::create([
			'name' => 'Development CGHMN Server',
			'interface_name' => 'wg-server',
			'private_key' => 'UIbfIStIKmoFC4V2Wyxu7Bw2laQvNX6tqprDKHurD0g=',
			'public_key' => 'y3auDCZWWgGKp2rPtwZHhYqbuKKRXAod1xGF84pCZAA=',
			'endpoint' => 'nowhere.example.org',
			'listen_port' => 51820,
			'tunnel_ip' => '192.0.2.1/24',
			'routed_subnet' => '240.0.0.0/16',
		]);
	}
}
