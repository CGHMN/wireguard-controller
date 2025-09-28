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
		$peer = Peer::firstOrFail();

		foreach(['192.168.0.0/24', '100.99.98.0/24'] as $cidr) {
			PeerAllowedIp::create([
				'cidr' => $cidr,
				'peer_id' => $peer->id
			]);
		}
    }
}
