<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		User::create([
			'name' => 'snep',
			'email' => 'cghmn@diskcat.com',
			'email_verified_at' => Carbon::now(),
			'password' => Hash::make('password')
		]);
    }
}
