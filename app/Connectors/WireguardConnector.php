<?php

namespace App\Connectors;

use App\Helpers\IpAddressHelper;
use App\Models\Peer;
use Exception;

use App\Models\Server;
use Illuminate\Support\Facades\Process;

class WireguardConnector
{
	/**
	 * Generate the Wireguard config for the local server
	 * 
	 * @param \App\Models\Server $server Server instance to generate the configuration for
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

			$config .= "AllowedIPs = ".join(', ', [$peer->tunnel_ip, ...$allowed_ips]);
			$config .= "\n\n";
		}

		return $config;
	}

	/**
	 * Creates a Wireguard interface and assigns IP addresses and routes
	 * 
	 * @param \App\Models\Server $server Server instance to bring online
	 * @return void
	 */
	public static function start_server(Server $server): void {
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
	 * @param \App\Models\Server $server Instance of Server model to get interface name from
	 * @param string $filename Filename of configuration to apply to interface, use default if left as null
	 * @return void
	 */
	public static function apply_config(Server $server): void {
		if (!is_dir("/sys/class/net/{$server->interface_name}")) {
			self::start_server($server);
			return;
		}

		Process::input(self::generate_server_config($server))
			->run("sudo /usr/bin/wg setconf '{$server->interface_name}' /dev/stdin")
			->throw();
	}

	/**
	 * Creates a configuration file for one of the server peers
	 * @param \App\Models\Server $server Instance of Server model to generate peer config with
	 * @param string $peer_id Database ID of Peer to generate peer config for
	 * @return string Wireguard configuration as multi-line string
	 */
	public static function generate_peer_config(Server $server, string $peer_id): string {
		$peer = Peer::where('server_id', $server->id)->findOrFail($peer_id);
		$server_tunnel_network = IpAddressHelper::network_address_from_cidr(
			$server->tunnel_ip
		);

		$allowed_ips = [
			$server_tunnel_network,
			$server->routed_subnet
		];

		foreach($server->allowed_ips as $ip) {
			$allowed_ips[] = $ip->cidr;
		}

		$allowed_ips_str = join(', ', $allowed_ips);

		return <<<PEER_CONFIG
		[Interface]
		Address = {$peer->tunnel_ip}
		PrivateKey = <insert-private-key-here>

		[Peer]
		PublicKey = {$server->public_key}
		AllowedIPs = {$allowed_ips_str}
		Endpoint = {$server->endpoint}:{$server->listen_port}
		\n
		PEER_CONFIG;
	}

	/**
	 * Generates a public and private peer key pair
	 * @return array Object of public and private keys as strings
	 */
	public static function generate_key_pair(): object {
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
