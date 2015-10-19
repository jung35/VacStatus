<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use VacStatus\Steam\SteamUser;

class SteamUserTest extends TestCase
{
    public function test_steamuser()
    {
    	$test = [
			'jung3o', 'http://steamcommunity.com/id/Jung3o/',
			'U:1:60051399', 'STEAM_0:1:30025699', '76561198020317127'
		];

		$expected = [
			'76561198020317127', '76561198020317127', '76561198020317127',
			'76561198020317127', '76561198020317127'
		];

		$this->assertEquals($expected, (new SteamUser($test))->fetch());
		$this->assertEquals($expected[0], (new SteamUser($test[0]))->fetch());
	}

	public function test_steamuser_invalid()
	{
		$this->assertEquals('Invalid Input', (new SteamUser([]))->fetch());
		$this->assertEquals('Invalid Input', (new SteamUser(['123iojasdfoijasdom']))->fetch());
		$this->assertEquals('Invalid Input', (new SteamUser(''))->fetch());
		$this->assertEquals('Invalid Input', (new SteamUser('123iojasdfoijasdom'))->fetch());
	}
}
