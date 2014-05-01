<?php

use Illuminate\Console\Command;

class vBanListChecker extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'vBanListChecker';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Checks a vBanList of a subscribed user.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$MailController = new MailController;
	}

}
