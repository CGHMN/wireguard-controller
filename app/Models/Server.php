<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
