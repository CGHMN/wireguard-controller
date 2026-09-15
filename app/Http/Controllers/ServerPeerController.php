<?php

namespace App\Http\Controllers;

use App\Connectors\WireguardConnector;
use App\Helpers\ServerHelper;
use App\Models\PeerAllowedIp;
use App\Models\Server;
use Illuminate\Http\Request;

use App\Models\Peer;

class ServerPeerController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(string $server_id)
	{
		$server = Server::findOrFail($server_id);

		return Peer::with('allowed_ips')
			->where('server_id', $server->id)
			->get();
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request, string $server_id)
	{
		$server = Server::findOrFail($server_id);
		return Peer::create([
			...$request->all(),
			'server_id' => $server->id
		]);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(string $server_id, string $peer_id)
	{
		$server = Server::findOrFail($server_id);

		return Peer::with('allowed_ips')
			->where('server_id', $server->id)
			->findOrFail($peer_id);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, string $server_id, string $peer_id)
	{
		$server = Server::findOrFail($server_id);

		$model = Peer::where('server_id', $server->id)->findOrFail($peer_id);
		$model->update($request->all());
		return $model;
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $server_id, string $peer_id)
	{
		$server = Server::findOrFail($server_id);

		return Peer::where('server_id', $server->id)
			->findOrFail($peer_id)
			->delete();
	}

	/**
	 * Generate the peer Wireguard configuration for the specified server and peer
	 */
	public function generate_peer_configuration(Request $request, string $server_id, string $peer_id)
	{
		$server = Server::findOrFail($server_id);
		$peer = Peer::findOrFail($peer_id);
		return WireguardConnector::generate_peer_config($server, $peer);
	}

	/**
	 * Generate a peer with most fields auto-generated
	 */
	public function generate_new_peer(Request $request, string $server_id)
	{
		$server = Server::findOrFail($server_id);

		$public_key = (string) $request->string('public_key');
		$private_key = null;

		if (!$public_key) {
			$keys = WireguardConnector::generate_key_pair();
			$public_key = $keys->public_key;
			$private_key = $keys->private_key;
		}

		$addresses = ServerHelper::next_free_ip_addresses($server);

		// Generate PSK only if query parameter 'no_psk' is not set
		$psk = null;
		if ($request->missing('no_psk') || $request->query('no_psk') == 'false') {
			$psk = WireguardConnector::generate_psk();
		}

		$peer = Peer::create([
			'name' => $request->string('name'),
			'public_key' => $public_key,
			'preshared_key' => $psk,
			'tunnel_ip' => $addresses->tunnel_ip->getDq(),
			'server_id' => $server->id
		]);

		PeerAllowedIp::create([
			'cidr' => "{$addresses->routed_subnet->getDq()}/{$addresses->routed_subnet->subnet()}",
			'peer_id' => $peer->id
		]);

		$peer->load('allowed_ips');

		$server->refresh();
		WireguardConnector::apply_config($server);

		if ($request->missing('return_config') || $request->input('return_config') == "false") {
			return $peer;
		}

		return WireguardConnector::generate_peer_config($server, $peer, $private_key);
	}
}
