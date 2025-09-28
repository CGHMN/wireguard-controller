<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peer extends Model
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
