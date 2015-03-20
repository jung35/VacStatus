<?php namespace VacStatus\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use VacStatus\Update\SubscriptionCheck;

use Pushbullet\Pushbullet;

use Cache;

class ListChecker extends Command
{
	protected $name = 'list';
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
		$lastCheckedSubscription = -1;
		if(Cache::has('last_checked_subscription')) $lastCheckedSubscription = Cache::pull('last_checked_subscription');

		$subscriptionCheck = new SubscriptionCheck($lastCheckedSubscription);

		Cache::forever('last_checked_subscription', $subscriptionCheck->setSubscription());

		$toSend = $subscriptionCheck->run();

		if(!$toSend)
		{
			$this->log->addInfo('Nothing was sent!');
			return;
		}

		if($toSend['send']['email'])
		{
			$email = $toSend['send']['email'];
			$profiles = $toSend['profiles'];

			Mail::send('emails.verification', [
				'profiles' => $profiles
			], function($message) use ($email) {
				$message->to($email)->subject('Bans were found from your subscribed lists!');
			});

			$this->log->addInfo('Email Sent!');
		}

		if($toSend['send']['pushbullet'])
		{
			$pushbullet = new Pushbullet(env('PUSHBULLET_API'));
			$profiles = $toSend['profiles'];

			foreach($profiles as $k => $profile)
			{
                if ($k + 1 != count($profiles)) $message .= $profile->display_name.", ";
                else $message .= (count($profiles) > 1 ? "and " : "") . $profile->display_name;
			}

			$message .= (count($profiles) > 1 ? " were " : " was")." Trade, Community, and/or VAC banned from your lists";

			$pushbullet
				->contact($toSend['send']['pushbullet'])
				->pushNote("Bans were found from your subscribed lists!", $message);

			$this->log->addInfo('Pushbullet Sent!');
		}

		$this->log->addInfo('EVERYTHING WAS SENT!');
	}

}
