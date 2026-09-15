<?php

return [
	'keepalive' => intval(env('WG_KEEPALIVE', 15)),
	'interface_management' => !((bool) env('WG_DISABLE_INTERFACE_MANAGEMENT', false)),
	'wg_endpoint_api' => [
		'push_enabled' => (bool) env('WG_ENDPOINT_API_HOSTS', false),
		'hosts' => strstr(env("WG_ENDPOINT_API_HOSTS", ""), '@') ?
			array_map(function ($value) {
				$split = explode('@', $value);
				return [
					'uri' => $split[1],
					'api_key' => $split[0]
				];
			}, explode(',', env("WG_ENDPOINT_API_HOSTS")))
			: []
	]
];
