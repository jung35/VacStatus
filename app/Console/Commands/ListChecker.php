<?php

namespace VacStatus\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use VacStatus\Update\SubscriptionCheck;

use PHPushbullet\PHPushbullet;

use Mail;
use Cache;
use Log;

class ListChecker extends Command
{
	protected $name = 'listCheck';
	protected $description = 'Checks subscription and sends email if a player is caught.';
	protected $checkerCacheName = "last_checked_subscription";
	protected $log;

	public function __construct()
	{
		parent::__construct();
		$this->log = \Log::getMonolog();
	}

	public function fire()
	{
		$log = $this->log;

		$subscriptionCheck = new SubscriptionCheck(Cache::pull('last_checked_subscription', -1));

		Cache::forever('last_checked_subscription', $subscriptionCheck->setSubscription());

		if(!$subscriptionCheck->run() && !in_array($subscriptionCheck->errorMessage(), ['no_small_ids_found', 'nothing_to_notify']))
		{
			if(!in_array($subscriptionCheck->errorMessage(), ['no_small_ids_found', 'nothing_to_notify']))
			{
				$log->error('VacStatus\Console\Commands\ListChecker', [
					'userMail ID' => $subscriptionCheck->setSubscription(),
					'message' => $subscriptionCheck->errorMessage()
				]);
			}

			return;
		}
		
		// send mail if email exists
		$subscriptionCheck->sendEmail(function($email, $profiles) use ($log)
		{
			Mail::send('emails.hacker', [
				'profiles' => $profiles
			], function($message) use ($email) {
				$message->to($email)->subject('Bans were found from your subscribed lists!');
			});

			$log->info('VacStatus\Console\Commands\ListChecker', [
			    'type' => 'sendEmail',
				'email' => $email,
			]);
		});

		// just like sending email, send pushbullet if the subscribed user has it
		$subscriptionCheck->sendPushBullet(function($email, $profiles) use ($log)
		{
			$pushbullet = new PHPushbullet(env('PUSHBULLET_API'));
			$message = "";

			foreach($profiles as $k => $profile)
			{
				if ($k + 1 != count($profiles)) $message .= $profile->display_name.", ";
				else $message .= (count($profiles) > 1 ? "and " : "") . $profile->display_name;
			}

			$message .= (count($profiles) > 1 ? " were " : " was")." VAC banned or Game banned from your lists";
			$pushbullet->user($email)->note("Bans were found from your subscribed lists!", $message);

			$log->info('VacStatus\Console\Commands\ListChecker', [
			    'type' => 'sendPushBullet',
				'email' => $email,
			]);
		});
	}

}
