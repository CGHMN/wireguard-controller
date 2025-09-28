<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;

use App\Models\ServerAllowedIp;

class ServerAllowedIpController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(string $server_id)
	{
		$server = Server::findOrFail($server_id);

		return ServerAllowedIp::where('server_id', $server->id)->get();
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request, string $server_id)
	{
		$server = Server::findOrFail($server_id);

		return ServerAllowedIp::create([
			...$request->all(),
			'server_id' => $server->id
		]);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(string $server_id, string $server_allowed_ip_id)
	{
		$server = Server::findOrFail($server_id);

		return ServerAllowedIp::where('server_id', $server->id)
			->findOrFail($server_allowed_ip_id);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, string $server_id, string $server_allowed_ip_id)
	{
		$server = Server::findOrFail($server_id);

		$model = ServerAllowedIp::where('server_id', $server->id)->findOrFail($server_allowed_ip_id);
		$model->update($request->all());
		return $model;
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $server_id, string $server_allowed_ip_id)
	{
		$server = Server::findOrFail($server_id);
		
		return ServerAllowedIp::where('server_id', $server->id)
			->findOrFail($server_allowed_ip_id)
			->delete();
	}
}
