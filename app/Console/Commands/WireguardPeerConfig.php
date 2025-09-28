<?php

namespace App\Console\Commands;

use App\Connectors\WireguardConnector;
use App\Models\Peer;
use App\Models\Server;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use function Laravel\Prompts\select;

class WireguardPeerConfig extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wireguard:peer_config {server_id} {peer_id}';

	/**
	 * Prompt for missing input arguments using the returned questions.
	 *
	 * @return array<string, string>
	 */
	protected function promptForMissingArgumentsUsing(): array
	{
		return [
			'server_id' => fn () => select(
				label: 'Which server shall the config be created from?',
				options: Server::pluck('name', 'id')
			),
			'peer_id' => fn () => select(
				label: 'Which peer shall the config be created for?',
				options: Peer::pluck('name', 'id')
			),
		];
	}

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Wireguard peer config';

    /**
     * Execute the console command.
     */
    public function handle()
    {
		$server_id = $this->argument('server_id');
		$server = Server::find($server_id);

		$peer_id = $this->argument('peer_id');
		$peer = Peer::where('server_id', $server->id)->find($peer_id);

		if (is_null($server)) {
			$this->fail("Server with ID {$server_id} does not exist");
		}

		if (is_null($peer)) {
			$this->fail("Peer with ID {$peer_id} in server '{$server->name}' not found");
		}

		$this->line(WireguardConnector::generate_peer_config($server, $peer->id));
    }
}
