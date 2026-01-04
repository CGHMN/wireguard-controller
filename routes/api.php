<?php

use App\Connectors\WireguardConnector;
use App\Http\Controllers\WireguardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Middleware\ApiKeyAuth;

use App\Http\Controllers\ServerController;
use App\Http\Controllers\ServerAllowedIpController;
use App\Http\Controllers\ServerPeerController;
use App\Http\Controllers\ServerPeerAllowedIpController;

Route::prefix('v1')
	->namespace('App\Http\Controllers')
	->middleware(ApiKeyAuth::class)
	->group(function () {
		Route::apiResource('servers', ServerController::class);
		Route::apiResource('servers.allowed_ips', ServerAllowedIpController::class);
		Route::apiResource('servers.peers', ServerPeerController::class);
		Route::apiResource('servers.peers.allowed_ips', ServerPeerAllowedIpController::class);

		Route::post('servers/{server}/reload', [WireguardController::class, 'reload']);
		Route::post('servers/{server}/gen_new_peer', [ServerPeerController::class, 'generate_new_peer']);
		Route::get('servers/{server}/peers/{peer}/config', [ServerPeerController::class, 'generate_peer_configuration']);
	});

Route::get('/user', function (Request $request) {
    return $request->user();
});