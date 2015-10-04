<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use VacStatus\Steam\Steam;

class SteamTest extends TestCase
{
	private $s64bitId = ['76561198020317127', '76561197993761807'];
	private $s32bitId = ['STEAM_0:1:30025699', 'STEAM_0:1:16748039'];
	private $smallId = [60051399, 33496079];

	public function test_for_api_key()
	{
		dump(Steam::class);
		$this->assertEquals(gettype(Steam::getAPI()), 'string');
	}

	public function test_if_api_key_is_real()
	{
		$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . Steam::getAPI() . "&steamids=76561197984911659";

		$client = new GuzzleHttp\Client();

		try {
			$request = $client->get($url, ['connect_timeout' => 0, 'timeout' => 10]);
		} catch(GuzzleHttp\Exception\ClientException $e) {
			$this->fail('400 level error thrown. API key was invalid');
		}

		$this->assertTrue(True);
	}

	public function test_profile_update()
	{
		$testId = 123;

		$this->assertTrue(Steam::canUpdate($testId));
		Steam::setUpdate($testId);
		$this->assertFalse(Steam::canUpdate($testId));
	}

	public function test_small_id_conversion_from_steam_64bit_id()
	{
		$this->assertEquals(Steam::toSmallId($this->s64bitId[0]), $this->smallId[0]);
		$this->assertEquals(Steam::toSmallId($this->s64bitId), $this->smallId);
	}

	public function test_steam_64bit_id_conversion_from_small_id()
	{
		$this->assertEquals(Steam::to64bit($this->smallId[0]), $this->s64bitId[0]);
		$this->assertEquals(Steam::to64bit($this->smallId), $this->s64bitId);
	}

	public function test_steam_32bit_id_conversion_from_steam_64bit_id()
	{
		$this->assertEquals(Steam::to32bit($this->s64bitId[0]), $this->s32bitId[0]);
		$this->assertEquals(Steam::to32bit($this->s64bitId), $this->s32bitId);
	}
}