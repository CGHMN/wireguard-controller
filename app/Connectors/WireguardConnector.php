<?php

namespace App\Connectors;

use App\Models\Peer;
use Exception;

use App\Models\Server;
use Illuminate\Support\Facades\Process;

class WireguardConnector
{
	/**
	 * Generate the Wireguard config for the local server
	 *
	 * @param Server $server Server instance to generate the configuration for
	 * @return string Wireguard config as multi-line string
	 */
	public static function generate_server_config(Server $server): string
	{
		$config = <<<EOF
		# WARNING: This configuration file is managed by the wireguard-controller API!
		# Any manual changes in this file may be overwritten at any time.

		[Interface]
		# Address  = {$server->tunnel_ip}
		ListenPort = {$server->listen_port}
		PrivateKey = {$server->private_key}
		\n
		EOF;

		// Write [Peer] section for each known peer
		foreach ($server->peers as $peer) {
			$config .= <<<EOF
			[Peer]
			# {$peer->name}
			PublicKey = {$peer->public_key}
			PersistentKeepalive = 15
			\n
			EOF;

			if ($peer->preshared_key) {
				$config .= "PresharedKey = {$peer->preshared_key}\n";
			}

			$allowed_ips = [];
			foreach ($peer->allowed_ips as $ip) {
				$allowed_ips[] = $ip->cidr;
			}

			$config .= "AllowedIPs = " . join(', ', [$peer->tunnel_ip, ...$allowed_ips]);
			$config .= "\n\n";
		}

		return $config;
	}

	/**
	 * Creates a configuration file for one of the server peers
	 * @param Server $server Instance of Server model to generate peer config with
	 * @param Peer $peer Peer to generate peer config for
	 * @param ?string $private_key Optional private key of the peer to embed into the config
	 * @return string Wireguard configuration as multi-line string
	 */
	public static function generate_peer_config(Server $server, Peer $peer, ?string $private_key = null): string
	{
		$allowed_ips_str = $server->allowed_ips->pluck('cidr')->join(',');

		if (!$private_key) {
			$private_key = '<insert-private-key-here>';
		}

		$output = <<<PEER_CONFIG
		[Interface]
		Address = {$peer->tunnel_ip}
		PrivateKey = {$private_key}
		PEER_CONFIG . "\n\n";

		$output .= <<<PEER_CONFIG
		[Peer]
		PublicKey = {$server->public_key}
		AllowedIPs = {$allowed_ips_str}
		Endpoint = {$server->endpoint}:{$server->listen_port}
		PersistentKeepalive = 15
		PEER_CONFIG;

		if ($peer->preshared_key) {
			$output .= "\nPresharedKey = {$peer->preshared_key}\n";
		}

		return $output;
	}

	/**
	 * Creates a Wireguard interface and assigns IP addresses and routes
	 *
	 * @param Server $server Server instance to bring online
	 * @return void
	 */
	public static function start_server(Server $server): void
	{
		// Do nothing if the interface management is disabled on this server
		if (!config('wireguard.interface_management')) {
			return;
		}

		if (is_dir("/sys/class/net/{$server->interface_name}")) {
			throw new Exception("Wireguard interface {$server->interface_name} already exists");
		}

		Process::run("sudo /sbin/ip link add '{$server->interface_name}' type wireguard")->throw();
		Process::run("sudo /sbin/ip address add '{$server->tunnel_ip}' dev '{$server->interface_name}'")->throw();
		Process::run("sudo /sbin/ip link set dev '{$server->interface_name}' up")->throw();
		Process::run("sudo /sbin/ip link set dev '{$server->interface_name}' mtu {$server->mtu}")->throw();

		Process::run("sudo /sbin/ip route add '{$server->routed_subnet}' dev '{$server->interface_name}'")->throw();

		self::apply_config($server);
	}

	/**
	 * Applies the given configuration file to an existing Wireguard interface
	 *
	 * @param Server $server Instance of Server model to get interface name from
	 * @return void
	 */
	public static function apply_config(Server $server): void
	{
		// Do nothing if the interface management is disabled on this server
		if (!config('wireguard.interface_management')) {
			return;
		}

		if (!is_dir("/sys/class/net/{$server->interface_name}")) {
			self::start_server($server);
			return;
		}

		Process::input(self::generate_server_config($server))
			->run("sudo /usr/bin/wg syncconf '{$server->interface_name}' /dev/stdin")
			->throw();

		// Add routes too
		$ip_route = json_decode(exec("ip --json route"));
		$known_routes = [];
		foreach ($ip_route as $route)
			$known_routes[] = explode('/', $route->dst)[0];
		foreach ($server->peers as $peer) {
			foreach ($peer->allowed_ips as $ip) {
				$ip_no_cidr = explode('/', $ip->cidr)[0];

				if (!in_array($ip_no_cidr, $known_routes)) {
					$known_routes[] = $ip_no_cidr;
					Process::run("sudo /sbin/ip route add {$ip->cidr} dev {$server->interface_name}")
						->throw();
				}
			}
		}
	}

	/**
	 * Generates a public and private peer key pair
	 * @return array Object of public and private keys as strings
	 */
	public static function generate_key_pair(): object
	{
		$private_key = Process::run('/usr/bin/wg genkey')
			->throw()
			->output();

		$public_key = Process::input($private_key)
			->run('/usr/bin/wg pubkey')
			->throw()
			->output();

		return (object) [
			'public_key' => trim($public_key),
			'private_key' => trim($private_key)
		];
	}

	/**
	 * Generates a preshared key
	 * @return string Preshared key as string
	 */
	public static function generate_psk(): string
	{
		return trim(Process::run('/usr/bin/wg genpsk')
			->throw()
			->output());
	}
}
