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
		$this->assertEquals('string', gettype(Steam::getAPI()));
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
		$this->assertEquals($this->smallId[0], Steam::toSmallId($this->s64bitId[0]));
		$this->assertEquals($this->smallId, Steam::toSmallId($this->s64bitId));
	}

	public function test_steam_64bit_id_conversion_from_small_id()
	{
		$this->assertEquals($this->s64bitId[0], Steam::to64bit($this->smallId[0]));
		$this->assertEquals($this->s64bitId, Steam::to64bit($this->smallId));
	}

	public function test_steam_32bit_id_conversion_from_steam_64bit_id()
	{
		$this->assertEquals($this->s32bitId[0], Steam::to32bit($this->s64bitId[0]));
		$this->assertEquals($this->s32bitId, Steam::to32bit($this->s64bitId));
	}

	public function test_alias_sort()
	{
		$test = [[
			"newname" => "test1",
			"timechanged" => "Aug 29 @ 7:03pm"
		], [
			"newname" => "test2",
			"timechanged" => "Sep 24 @ 10:32pm"
		]];

		$expected = [[
			"newname" => "test2",
			"timechanged" => "Sep 24 @ 10:32pm"
		], [
			"newname" => "test1",
			"timechanged" => "Aug 29 @ 7:03pm"
		]];

		usort($test, array('VacStatus\Steam\Steam', 'aliasSort'));

		$this->assertEquals($expected, $test);
	}

	public function test_alias_time_convert()
	{
		$test = "Aug 29 @ 7:03pm";
		$expected = 1440900180;

		$this->assertEquals($expected, Steam::aliasTimeConvert($test));
	}

	public function test_friendly_alias_conversion()
	{
		$test = [[
			"newname" => "test1",
			"timechanged" => "Aug 29 @ 7:03pm"
		]];

		$expected =[[
			"newname" => "test1",
			"timechanged" => "Aug 29 2015"
		]];

		$this->assertEquals($expected, Steam::friendlyAlias($test));
	}

	public function test_search_parser()
	{
		$test = "76561198000020858 jung3o\n76561198031554200";
		$expected = ['76561198000020858', 'jung3o', '76561198031554200'];

		$this->assertEquals($expected, Steam::parseSearch($test));

		$test = <<<EOT
hostname: Jung
version : 1.35.0.4/13504 6171 secure  
udp/ip  : 192.168.10.1:27015
os      :  Windows
type    :  listen
map     : de_dust2 at: -367 x, -808 y, 161 z
players : 1 humans, 9 bots (20/0 max) (not hibernating)

# userid name uniqueid connected ping loss state rate adr
#  2 1 "Jung" STEAM_1:1:30025699 00:05 16 3 active 128000 loopback
# 3 "Jon" BOT active
# 4 "Irving" BOT active
# 5 "Will" BOT active
# 6 "Shawn" BOT active
# 7 "Andy" BOT active
# 8 "Dean" BOT active
# 9 "Norm" BOT active
#10 "Ron" BOT active
#11 "Yahn" BOT active
#end
EOT;

		$expected = ['STEAM_1:1:30025699'];

		$this->assertEquals($expected, Steam::parseSearch($test));
	}
}