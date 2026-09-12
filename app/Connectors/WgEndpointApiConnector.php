<?php

namespace App\Connectors;

use App\Helpers\WgEndpointApiHelper;
use App\Models\Server;
use Http;

class WgEndpointApiConnector
{
	/**
	 * Push a Wireguard server configuration to all configured endpoints
	 * @param Server $server Wireguard server to push as updated configuration to the endpoints
	 * @return void
	 */
	public static function pushToEndpoints(Server $server): void
	{
		if (!config('wireguard.wg_endpoint_api.push_enabled')) {
			\Log::debug('Push to Endpoints: Push not enabled');
			return;
		}

		$configuration = WgEndpointApiHelper::serverToArray($server);

		foreach (config('wireguard.wg_endpoint_api.hosts') as $endpoint) {
			Http::withHeader('X-API-Key', $endpoint['api_key'])
				->post("{$endpoint['uri']}/interfaces/{$server->interface_name}/sync", $configuration);
		}
	}
}
