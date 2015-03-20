<?php namespace VacStatus\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Cache;
use VacStatus\Steam\SteamAPI;
use VacStatus\Models\Profile;

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

		$profiles = Profile::whereIn('small_id', $smallIds)->get();

		foreach($profiles as $profile)
		{
			$steamAPI = new SteamAPI('alias');
			$steamAPI->setSmallId($profile->small_id);
			$steamAlias = $steamAPI->run();

			if($steamAPI->error()) { $steamAlias = []; }
			else { usort($steamAlias, array('VacStatus\Steam\Steam', 'aliasSort')); }

			$profile->alias = json_encode($steamAlias);
			$profile->save();
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
