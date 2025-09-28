<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Server;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
		return Server::with(['allowed_ips'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
		return Server::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
		return Server::with(['peers', 'allowed_ips'])
			->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
		$model = Server::findOrFail($id);
		$model->update($request->all());
		return $model;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
		return Server::findOrFail($id)->delete();
    }
}
