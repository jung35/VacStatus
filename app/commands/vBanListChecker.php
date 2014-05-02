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
		$mailList = $MailController->getASubscribedUser();
		$steamUser = steamUser::whereId($mailList->steam_user_id)->first();
		$vBanUser = $steamUser->vBanUser;
		$vBanList = $steamUser->vBanList;

		$this->info("");
		$this->info("");
		$this->info("========== User Found! [ID {$mailList->steam_user_id}] ==========");
		$this->info("");
		$this->info("     Display Name:    {$vBanUser->display_name}");
		$this->info("     Community ID:    {$vBanUser->community_id}");
		$this->info("           E-Mail:    {$mailList->email}");
		$this->info("    Users in List:    {$vBanList->count()}");
		$this->info("");
		$this->info("... Checking & Sending E-Mail ...");

		if($MailController->checkUserList($vBanList, $mailList->steam_user_id)) {
			$this->info("");
			$this->info("Email SENT!");
			$this->info("");
		} else {
			$this->info("");
			$this->info("Email Wasn't needed!");
			$this->info("");
		}
		$this->info("========================================");
		$this->info("");
		$this->info("");

		return;
	}

}
