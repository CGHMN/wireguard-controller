<?php

namespace App\Http\Controllers;

use App\Helpers\WgEndpointApiHelper;
use App\Models\Server;

class WgEndpointApiController
{
	/**
	 * Convert a server instance into an array representation as expected by the WG Endpoint API
	 * based on its Wireguard interface name in database
	 * @param string $interface_name Name of the Wireguard interface to search for
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
	public function getInterfaceConfiguration(string $interface_name): array
	{
		$server = Server::where('interface_name', '=', $interface_name)->firstOrFail();
		return WgEndpointApiHelper::serverToArray($server);
	}
}
