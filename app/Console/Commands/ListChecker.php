<?php namespace VacStatus\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use VacStatus\Update\SubscriptionCheck;

use PHPushbullet\PHPushbullet;

use Mail;
use Cache;

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
		$subscriptionCheck = new SubscriptionCheck(Cache::pull('last_checked_subscription', -1));

		Cache::forever('last_checked_subscription', $subscriptionCheck->setSubscription());

		if(!$subscriptionCheck->run())
		{
			// $subscriptionCheck->errorMessage();
			return; // Todo: Log this
		}
		
		// send mail if email exists
		$subscriptionCheck->sendEmail(function($email, $profiles)
		{
			Mail::send('emails.hacker', [
				'profiles' => $profiles
			], function($message) use ($email) {
				$message->to($email)->subject('Bans were found from your subscribed lists!');
			});

		});

		// just like sending email, send pushbullet if the subscribed user has it
		$subscriptionCheck->sendPushBullet(function($email, $profiles)
		{
			$pushbullet = new PHPushbullet(env('PUSHBULLET_API'));
			$message = "";

			foreach($profiles as $k => $profile)
			{
                if ($k + 1 != count($profiles)) $message .= $profile->display_name.", ";
                else $message .= (count($profiles) > 1 ? "and " : "") . $profile->display_name;
			}

			$message .= (count($profiles) > 1 ? " were " : " was")." Trade, Community, and/or VAC banned from your lists";
			$pushbullet->user($email)->note("Bans were found from your subscribed lists!", $message);

		});
	}

}
