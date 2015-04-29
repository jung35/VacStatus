<?php namespace VacStatus\Http\Controllers\APIv1;

use Illuminate\Http\Request;

use VacStatus\Http\Controllers\Controller;

use VacStatus\Models\DonationLog;
use VacStatus\Models\DonationPerk;
use VacStatus\Models\User;

use Mdb\PayPal\Ipn\Event\MessageVerifiedEvent;
use Mdb\PayPal\Ipn\ListenerBuilder\Guzzle\InputStreamListenerBuilder as ListenerBuilder;


use Auth;

use VacStatus\Steam\Steam;

class DonationController extends Controller
{
	public function index()
	{
		$latestDonation = DonationLog::whereStatus('Completed')
			->leftjoin('users', 'donation_log.small_id', '=', 'users.small_id')
			->orderBy('donation_log.id', 'desc')
			->take(10)
			->get([
				'donation_log.original_amount',

				'users.display_name',
				'users.small_id',

				'users.donation',
				'users.beta',
				'users.site_admin',
			]);

		$latestDonationParsed = [];

		foreach($latestDonation as $donation)
		{
			$latestDonationParsed[] = [
				'original_amount' => $donation->original_amount,
				'display_name' => $donation->display_name,
				'small_id' => $donation->small_id,
				'steam_64_bit' => Steam::to64bit($donation->small_id),

				'donation' => $donation->donation,
				'beta' => $donation->beta,
				'site_admin' => $donation->site_admin,
			];
		}

		$latestDonation = $latestDonationParsed;

		$mostDonation = User::where('donation', '>', '0')
			->orderBy('donation', 'desc')
			->take(10)
			->get([
				'users.display_name',
				'users.small_id',

				'users.donation',
				'users.beta',
				'users.site_admin',
			]);

		$mostDonationParsed = [];

		foreach($mostDonation as $donation)
		{
			$mostDonationParsed[] = [
				'display_name' => $donation->display_name,
				'small_id' => $donation->small_id,
				'steam_64_bit' => Steam::to64bit($donation->small_id),

				'donation' => $donation->donation,
				'beta' => $donation->beta,
				'site_admin' => $donation->site_admin,
			];
		}

		$mostDonation = $mostDonationParsed;

		$donationPerk = DonationPerk::orderBy('amount', 'asc')->get();

		$user = null;

		if(Auth::check())
		{
			$user = Auth::user()->toArray();
			unset($user['remember_token']);
		}

		return compact(
			'user',
			'latestDonation',
			'mostDonation',
			'donationPerk'
		);
	}

	public function IPNAction()
	{
		$listener = new ListenerBuilder;

		$listener->useSandbox();
		$listener = $listener->build();

		$listener->onVerified(function (MessageVerifiedEvent $event) {
			$ipnMessage = $event->getMessage();
			if($ipnMessage->get('payment_status') != 'Completed') return;

			$original_amount = $ipnMessage->get('mc_gross');
			$amount_sub = $ipnMessage->get('mc_fee');
			$smallId = $ipnMessage->get('custom');

			$donationLog = new DonationLog;
			$donationLog->status = 'Completed';
			$donationLog->amount = $original_amount - $amount_sub;
			$donationLog->original_amount = $original_amount;

			if(is_numeric($smallId))
			{
				$user = User::whereSmallId($smallId)->first();
				if(isset($user->id))
				{
					$user->donation += $original_amount;
					$user->save();
					$donationLog->small_id = $smallId;
				}
			}

			$donationLog->save();
		});

		$listener->listen();
	}
}