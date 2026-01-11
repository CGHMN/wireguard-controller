<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Server
 * 
 * @property-read integer $id Database ID of the server instance
 * @property string $name Human readable name of the instance
 * @property string $interface_name Name of the Linux kernel Wireguard interface
 * @property string $private_key Wireguard private key
 * @property string $public_key Wireguard public key
 * @property string $endpoint Public server IP or hostname
 * @property integer $listen_port Port the server listens on
 * @property integer $mtu Tunnel MTU value
 * @property integer $tunnel_ip Inner tunnel IP address of server
 * @property integer $routed_subnet Subnet from which smaller subnets are routed to members
 */
class Server extends Model
{
	/**
	 * Mass-assignable variables
	 * 
	 * @var array<string>
	 */
	protected $fillable = [
		'interface_name',
		'endpoint',
		'listen_port',
		'tunnel_ip',
		'tunnel_netmask',
		'routed_subnet_address',
		'routed_subnet_netmask',
		'mtu',
		'name'
	];

	/**
	 * Attributes hidden from serialization of model
	 * 
	 * @var array<string>
	 */
	protected $hidden = [
		'private_key'
	];

	/**
	 * Attributes appended to serialization of model
	 * 
	 * @var array<string>
	 */
	protected $appends = [];

	/**
	 * Explicitly cast attributes
	 * 
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [];
	}

	/**
	 * Peers attached to this server
	 */
	public function peers(): HasMany
	{
		return $this->hasMany(Peer::class);
	}

	/**
	 * Allowed Ips routed to this server
	 */
	public function allowed_ips(): HasMany
	{
		return $this->hasMany(ServerAllowedIp::class);
	}
}
