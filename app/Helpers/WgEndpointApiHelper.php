<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Server;

class WgEndpointApiHelper
{
	/**
	 * Convert a server instance object into an array representation as expected by the WG Endpoint API
	 * @param Server $server Server instance object to convert
	 * @return array{
	 *   private_key: string,
	 *   address: string,
	 *   listen_port: int,
	 *   mtu: ?int,
	 *   peers: array{
	 *     public_key: string,
	 *     preshared_key: ?string,
	 *     persistent_keepalive: int,
	 *     name: string,
	 *     allowed_ips: array<string>
	 *   }
	 * }
	 */
	public static function serverToArray(Server $server): array
	{
		return [
			'private_key' => $server->private_key,
			'address' => $server->tunnel_ip,
			'listen_port' => $server->listen_port,
			'mtu' => $server->mtu,
			'peers' => $server->peers->map(fn($peer) => [
				'public_key' => $peer->public_key,
				'preshared_key' => $peer->preshared_key,
				'persistent_keepalive' => config('wireguard.keepalive'),
				'name' => $peer->name,
				'allowed_ips' => [
					$peer->tunnel_ip,
					...$peer->allowed_ips->map(fn($allowed_ip) => $allowed_ip->cidr)->toArray()
				]
			])->toArray()
		];
	}
}
