<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read integer $id Database ID of this allowed IP entry
 * @property-read \DateTime $created_at Creation timestamp of this allowed IP entry in database
 * @property-read \DateTime $updated_at Last update timestamp of this allowed IP entry in database
 * @property string $cidr CIDR notation of the allowed IP (subnet)
 * @property int $peer_id ID of the peer this allowed IP entry is assigned to
 * @property Peer $peer Peer this allowed IP entry is assigned to
 */
class PeerAllowedIp extends WireguardComponentBaseModel
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
