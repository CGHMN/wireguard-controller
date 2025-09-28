<?php

namespace Database\Seeders;

use App\Models\Server;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		Server::create([
			'name' => 'Example Primary Server',
			'interface_name' => 'wg0',
			'private_key' => '2PgyWuQ9sV/DwBuJNZhhBPoqAKOyjiaNp7WIwlyL4Vc=',
			'public_key' => 'TESTpJs8CQXLweUzDZ1iRUJaEUHT82fKkcNuueTTtnU=',
			'endpoint' => 'wg.example.org',
			'listen_port' => 51820,
			'tunnel_ip' => '172.24.0.1/24',
			'routed_subnet' => '192.168.0.0/16',
		]);
    }
}
