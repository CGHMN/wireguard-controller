<?php

namespace App\Models;

use App\Connectors\WgEndpointApiConnector;
use Illuminate\Database\Eloquent\Model;

class WireguardComponentBaseModel extends Model
{
	protected static function booted()
	{
		// Push updates to all WG Endpoint API nodes on updates to any Wireguard components
		static::created(fn(Model $model) => self::pushUpdateToWgEndpointApi($model));
		static::updated(fn(Model $model) => self::pushUpdateToWgEndpointApi($model));
		static::deleted(fn(Model $model) => self::pushUpdateToWgEndpointApi($model));

		parent::booted();
	}

	/**
	 * Push updates to all WG Endpoint API nodes depending on their Model type
	 * @param Model $model Model data to push updates for
	 * @return void
	 */
	protected static function pushUpdateToWgEndpointApi(Model $model): void
	{
		if (!config('wireguard.wg_endpoint_api.push_enabled')) {
			return;
		}

		switch (\get_class($model)) {
			case Server::class:
				/** @var Server $model */
				WgEndpointApiConnector::pushToEndpoints($model);
				break;
			case Peer::class:
				/** @var Peer $model */
				WgEndpointApiConnector::pushToEndpoints($model->server);
				break;
			case ServerAllowedIp::class:
				/** @var ServerAllowedIp $model */
				WgEndpointApiConnector::pushToEndpoints($model->server);
				break;
			case PeerAllowedIp::class:
				/** @var PeerAllowedIp $model */
				WgEndpointApiConnector::pushToEndpoints($model->peer->server);
				break;
		}
	}
}
