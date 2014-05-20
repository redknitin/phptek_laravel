<?php

use Models\User;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UserTableSeeder');
	}

}

class UserTableSeeder extends Seeder {

	public function run()
	{
		User::create([
			'name'     => 'Admin',
		    'username' => 'admin',
		    'password' => 'password',
		    'email'    => 'admin@localhost',
		    'status'   => 'enabled',
		]);
	}

}
