<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use VacStatus\Steam\SteamUser;

class SteamUserTest extends TestCase
{

    public function test_start()
    {
		dump(SteamUser::class);
    }
}
