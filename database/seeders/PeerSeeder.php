<?php

namespace Database\Seeders;

use App\Models\Peer;
use App\Models\Server;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$server = Server::firstOrFail();

		Peer::create([
			'name' => 'First Peer',
			'public_key' => 'TESTydX+WncYg/IqnH57NbrmJvQMNbN98KqMNbgbRiM=', // eKLQcZcJ/bUyN+6jJ8uU09qN0Id57A5S3X7Ybteq+nc=
			'preshared_key' => 'OeDX8SGpsNrEaIK5uQcz+LwU3176CIm9i3ItJ6Xk9Nk=',
			'tunnel_ip' => '172.24.0.2/32',
			'server_id' => $server->id
		]);

		Peer::create([
			'name' => 'Second Peer',
			'public_key' => 'zdzCo53++UQH1NOEswo6xqImwY/1DIKR9Gi3OgFJhWk=', // MBKYago6Q2r59rJ73QtckB7G+XkOWaFER+JQoKcbCXo=
			'preshared_key' => 'A5Il3gIisFctra72wGFKqH+Z4V94UU3481oMo9AvVmk=',
			'tunnel_ip' => '172.24.0.4/32',
			'server_id' => $server->id
		]);

		Peer::create([
			'name' => 'Third Peer',
			'public_key' => 'HSS27FeUTrm7JS6hv4T7S57CRPYFB6anFZxSL50tgFo=', // IFAfN23Wyg9K7vJPgrcTi4DSmor9bfNDW8+dZBRMF08=
			'preshared_key' => 'TmPsq7Twh9js0LuyqQbAkp4gWfc+VlAaNo87rBPbEoI=',
			'tunnel_ip' => '172.24.0.5/32',
			'server_id' => $server->id
		]);
    }
}
