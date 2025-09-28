<?php

namespace App\Http\Controllers;

use App\Connectors\WireguardConnector;
use App\Models\Server;
use Illuminate\Http\Request;

class WireguardController extends Controller
{
    public function reload(Request $request, string $server_id)
    {
		$server = Server::findOrFail($server_id);
		WireguardConnector::apply_config($server);
		return true;
    }
}
