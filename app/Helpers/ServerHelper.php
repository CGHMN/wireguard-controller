<?php

namespace App\Helpers;

use App\Models\Peer;
use App\Models\PeerAllowedIp;
use App\Models\Server;
use Exception;
use Log;

class ServerHelper
{
	/**
	 * Finds the next free peer tunnel IP of a server
	 * @param \App\Models\Server $server Server instance to process
	 * @return string Tunnel IP in CIDR notation (/32 mask)
	 */
	public static function get_next_tunnel_ip(Server $server): string
	{
		$first_address = ip2long(
			IpAddressHelper::network_address_from_cidr($server->tunnel_ip, false)
		) + 2;
		$last_address = ip2long(
			IpAddressHelper::broadcast_address_from_cidr($server->tunnel_ip, false)
		) - 1;

		$existing_ips = [];

		foreach (Peer::get()->pluck('tunnel_ip')->toArray() as $ip) {
			$existing_ips[] = ip2long(IpAddressHelper::strip_cidrmask($ip));
		}

		for ($i = $first_address; $i <= $last_address; $i++) {
			if (! in_array($i, $existing_ips))
				return long2ip($i).'/32';
		}

		throw new Exception('No free IP address is currently available in this tunnel network');
	}

	/**
	 * Finds the next free peer routed subnet of a server
	 * @param \App\Models\Server $server Server instance to process
	 * @return string Routed subnet in CIDR notation
	 */
	public static function get_next_routed_subnet(Server $server, $size = 24): string
	{
		$ips_per_subnet = (2 ** (32 - $size));

		$first_address = ip2long(
			IpAddressHelper::network_address_from_cidr($server->routed_subnet, false)
		);
		$last_address = ip2long(
			IpAddressHelper::broadcast_address_from_cidr($server->routed_subnet, false)
		) - $ips_per_subnet - 1;

		$existing_ips = [];

		foreach (PeerAllowedIp::get() as $allowed_ip) {
			$existing_ips[] = ip2long(IpAddressHelper::strip_cidrmask($allowed_ip->cidr));
		}

		for ($i = $first_address; $i <= $last_address; $i += $ips_per_subnet) {
			if (! in_array($i, $existing_ips))
				return long2ip($i)."/{$size}";
		}

		throw new Exception('No free routed subnet is currently available on this server');
	}
}