<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read integer $id Database ID of this peer
 * @property-read \DateTime $created_at Creation timestamp of this peer in database
 * @property-read \DateTime $updated_at Last update timestamp of this peer in database
 * @property string $name Descriptive name of this peer
 * @property string $public_key Public key of this peer
 * @property ?string $preshared_key Preshared key of this peer
 * @property string $tunnel_ip Tunnel IP of this peer on the primary tunnel network
 * @property ?int $user_id ID of the user this peer is assigned to
 * @property int $server_id ID of the server this peer is registered on
 * @property-read Server $server Server this peer is registered on
 * @property-read Collection<PeerAllowedIp> $allowed_ips List of allowed IP entries for this peer
 */
class Peer extends WireguardComponentBaseModel
{
	/**
	 * Mass-assignable variables
	 *
	 * @var array<string>
	 */
	protected $fillable = [
		'public_key',
		'preshared_key',
		'tunnel_ip',
		'name',
		'server_id'
	];

	/**
	 * Attributes hidden from serialization of model
	 *
	 * @var array<string>
	 */
	protected $hidden = [];

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
	 * Tunnel Server this peer belongs to
	 */
	public function server(): BelongsTo
	{
		return $this->belongsTo(Server::class);
	}

	/**
	 * User this tunnel belongs to
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Allowed Ips routed to this peer
	 */
	public function allowed_ips(): HasMany
	{
		return $this->hasMany(PeerAllowedIp::class);
	}
}
