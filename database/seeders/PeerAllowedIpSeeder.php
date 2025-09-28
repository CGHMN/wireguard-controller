<?php

namespace Database\Seeders;

use App\Models\Peer;
use App\Models\PeerAllowedIp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeerAllowedIpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$peer = Peer::findOrFail(1);

		foreach(['192.168.0.0/24', '192.168.1.0/24'] as $cidr) {
			PeerAllowedIp::create([
				'cidr' => $cidr,
				'peer_id' => $peer->id
			]);
		}

		$peer = Peer::findOrFail(2);

		PeerAllowedIp::create([
			'cidr' => '192.168.2.0/24',
			'peer_id' => $peer->id
		]);

		$peer = Peer::findOrFail(3);

		PeerAllowedIp::create([
			'cidr' => '192.168.3.0/24',
			'peer_id' => $peer->id
		]);
    }
}
