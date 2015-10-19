<?php namespace VacStatus\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'VacStatus\Console\Commands\UpdateMultiAlias',
		'VacStatus\Console\Commands\ListChecker',
		'VacStatus\Console\Commands\profileBanConvert',
		'VacStatus\Console\Commands\announce',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('listCheck')->cron('*/1 * * * *');
	}

}
