<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeerAllowedIp extends Model
{
	/**
	 * Mass-assignable variables
	 * 
	 * @var array<string>
	 */
	protected $fillable = [
		'cidr',
		'peer_id'
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
	 * Tunnel Peer this address belongs to
	 */
	public function peer(): BelongsTo
	{
		return $this->belongsTo(Peer::class);
	}
}
