<?php

namespace App\Helpers;

use IPCalc\Address;

use App\Models\Server;
use IPCalc\Network;

class ServerHelper
{
	/**
	 * Get the next free tunnel and routed subnet IP addresses from the given server instance
	 * @param Server $server Server instance to get the addresses from
	 * @param int $routed_subnet_cidr_size Size of the resulting routed subnet
	 * @return object{
	 *   tunnel_ip: Address,
	 *   routed_subnet: Network
	 * }
	 */
	public static function next_free_ip_addresses(Server $server, int $routed_subnet_cidr_size = 24): object
	{
		$server_tunnel_network = new Network($server->tunnel_ip);
		$server_routed_network = new Network($server->routed_subnet);

		$last_tunnel_ip = $server->peers
			// Turn each tunnel IP for peer into an Address object
			->map(fn($e) => (new Address($e->tunnel_ip, 32)))
			// Filter out addresses which do not lie within the server tunnel network, like single IP allocations
			->filter(fn($e) => $server_tunnel_network->contains($e))
			// Add the server tunnel IP itself as the lowest used IP address limit
			->add(new Address($server->tunnel_ip, 32))
			// Sort tunnel IP addresses in ascending order
			->sort(fn($a, $b) => $a->getAddress() - $b->getAddress())
			// Get the largest (and thus last used) IP address from the list
			->last();

		if (!$last_tunnel_ip) {
			// No existing tunnel IP found, this should not happen as the server itself is also counted
			throw new \Exception("No existing tunnel IP addresses found on server '{$server->name}' (#{$server->id})");
		}

		if ($last_tunnel_ip->getAddress() >= $server_tunnel_network->lastHost()->getAddress()) {
			// Last tunnel IP is the last possible IP in the subnet, no more IPs are available
			throw new \Exception("No more free tunnel IP addresses available on server '{$server->name}' (#{$server->id})");
		}

		$free_tunnel_ip = new Address($last_tunnel_ip->getAddress() + 1, 32);
		$free_routed_subnet = new Network(
			$server_routed_network->getAddress() + ($free_tunnel_ip->getAddress() - $server_tunnel_network->getAddress() + 1) * 256,
			$routed_subnet_cidr_size
		);

		// Ensure neither the tunnel IP nor routed subnet are actually in use
		foreach ($server->peers as $peer) {
			if ($free_tunnel_ip->eq($peer->tunnel_ip)) {
				throw new \Exception("The next free tunnel IP {$free_tunnel_ip->getDq()} already belongs to peer '{$peer->name}' on server '{$server->name}' (# {$server->id})");
			}

			foreach ($peer->allowed_ips as $peer_allowed_ip) {
				if ($free_routed_subnet->checkCollision(new Address($peer_allowed_ip->cidr))) {
					throw new \Exception(
						"Collision detected between next free routed subnet {$free_routed_subnet->getDq()}/{$free_routed_subnet->subnet()} " .
						"and allowed IP entry {$peer_allowed_ip->cidr} for peer '{$peer->name}' on server '{$server->name}' (# {$server->id})"
					);
				}
			}
		}

		return (object) [
			'tunnel_ip' => $free_tunnel_ip,
			'routed_subnet' => $free_routed_subnet

		];
	}
}
