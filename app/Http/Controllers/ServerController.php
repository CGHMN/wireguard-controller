<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

use App\Models\Server;

class ServerController extends Controller
{
    /**
     * Fetch a listing of all existing Wireguard server instances.
	 * 
	 * @return Collection<Server> List of Wireguard server instances
     */
    public function index()
    {
		return Server::with(['allowed_ips'])->get();
    }

    /**
     * Store a newly created Wireguard server instance in storage.
	 * 
	 * @return Server Newly created Wireguard server instance
     */
    public function store(Request $request)
    {
		return Server::create($request->all());
    }

    /**
     * Fetch a Wireguard server instance.
	 * 
	 * @return Server Wireguard server instance
     */
    public function show(string $id)
    {
		return Server::with(['peers', 'allowed_ips'])
			->findOrFail($id);
    }

    /**
     * Update a Wireguard server instance.
	 * 
	 * @return Server Updated Wireguard server instance
     */
    public function update(Request $request, string $id)
    {
		$model = Server::findOrFail($id);
		$model->update($request->all());
		return $model;
    }

    /**
     * Remove a Wireguard server instance from storage.
	 * 
	 * @return true Always returns true
     */
    public function destroy(string $id)
    {
		return Server::findOrFail($id)->delete();
    }
}
