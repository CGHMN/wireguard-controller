<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PeerAllowedIp;
use App\Models\Peer;
use App\Models\Server;

class ServerPeerAllowedIpController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(string $server_id, string $peer_id)
	{
		$server = Server::findOrFail($server_id);
		$peer = Peer::findOrFail($peer_id);

		return PeerAllowedIp::where('peer_id', $peer->id)
			->whereRelation('peer', 'server_id', '=', $server->id)
			->get();
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request, string $server_id, string $peer_id)
	{
		$server = Server::findOrFail($server_id);
		$peer = Peer::findOrFail($peer_id);

		return PeerAllowedIp::create([
			...$request->all(),
			'peer_id' => $peer->id
		]);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(string $server_id, string $peer_id, string $peer_allowed_ip_id)
	{
		$server = Server::findOrFail($server_id);
		$peer = Peer::findOrFail($peer_id);

		return PeerAllowedIp::where('peer_id', $peer->id)
			->whereRelation('peer', 'server_id', '=', $server->id)
			->findOrFail($peer_allowed_ip_id);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, string $server_id, string $peer_id, string $peer_allowed_ip_id)
	{
		$server = Server::findOrFail($server_id);
		$peer = Peer::findOrFail($peer_id);

		$model = PeerAllowedIp::where('peer_id', $peer->id)
			->whereRelation('peer', 'server_id', '=', $server->id)
			->findOrFail($peer_allowed_ip_id);
		$model->update($request->all());
		return $model;
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $server_id, string $peer_id, string $peer_allowed_ip_id)
	{
		$server = Server::findOrFail($server_id);
		$peer = Peer::findOrFail($peer_id);
		
		return PeerAllowedIp::where('peer_id', $peer->id)
			->whereRelation('peer', 'server_id', '=', $server->id)
			->findOrFail($peer_allowed_ip_id)
			->delete();
	}
}
