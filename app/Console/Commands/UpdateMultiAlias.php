<?php namespace VacStatus\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use VacStatus\Steam\Steam;
use VacStatus\Steam\SteamAPI;
use VacStatus\Models\Profile;

use Cache;
use Carbon;

class UpdateMultiAlias extends Command {

	protected $queueCacheName = "update_alias_";
	protected $name = 'update:alias';
	protected $description = 'Command description.';

	public function __construct()
	{
		parent::__construct();
	}

	public function fire()
	{
		$cacheName = $this->queueCacheName.$this->argument('aliasCacheCode');
		if(!Cache::has($cacheName)) return;

		$smallIds = Cache::pull($cacheName);
		
		foreach(array_chunk($smallIds, 100) as $chunkedSmallIds)
		{

			$profiles = Profile::whereIn('small_id', $chunkedSmallIds)->get();

			foreach($profiles as $profile)
			{
				$steamAPI = new SteamAPI($profile->small_id, true);
				$steamAlias = $steamAPI->fetch('alias');

				if(!isset($steamAlias['error'])) usort($steamAlias, array('VacStatus\Steam\Steam', 'aliasSort'));
				else $steamAlias = [];

				$profile->alias = json_encode($steamAlias);
				$profile->save();

				$cacheName = "profile_{$profile->small_id}";

				if(!Cache::has($cacheName)) continue;
				
				$profileCache = Cache::get($cacheName);
				$profileCache['alias'] = Steam::friendlyAlias($steamAlias);

				if(Cache::has($cacheName)) Cache::forget($cacheName);

				$expireTime = Carbon::now()->addMinutes(60);

				Cache::put($cacheName, $profileCache, $expireTime);
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['aliasCacheCode', InputArgument::REQUIRED, 'Code of the saved cache of smallIds for updating alias'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}

}
