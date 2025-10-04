<?php

namespace App\Console\Commands;

use App\Connectors\WireguardConnector;
use App\Models\Server;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use function Laravel\Prompts\select;

class WireguardServerConfig extends Command implements PromptsForMissingInput
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'wireguard:server_config {server_id}';

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
			)
		];
	}

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate a Wireguard server config';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$server_id = $this->argument('server_id');
		$server = Server::find($server_id);

		if (is_null($server)) {
			$this->fail("Server with ID {$server_id} does not exist");
		}

		$this->line(WireguardConnector::generate_server_config($server));
	}
}
