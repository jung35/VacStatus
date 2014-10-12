<?php
use PayPal\Ipn\Listener;
use PayPal\Ipn\Message;
use PayPal\Ipn\Verifier\CurlVerifier;

class DonationController extends \BaseController {

  public function DonationAction() {

    $latestDonation = DonationLog::whereStatus('Completed')
      ->leftjoin('users', 'donation_log.small_id', '=', 'users.small_id')
      ->orderBy('donation_log.id', 'desc')
      ->take(10)
      ->get([
        'donation_log.original_amount',
        'users.display_name',
        'users.small_id',
        'users.donation',
        ]);

    $mostDonation = User::where('donation', '>', '0')
      ->orderBy('donation', 'desc')
      ->take(10)
      ->get();

    $donationPerk = DonationPerk::orderBy('id', 'asc')
      ->get();

    return View::make('donation/donation', Array(
                      'latestDonation' => $latestDonation,
                      'mostDonation' => $mostDonation,
                      'donationPerk' => $donationPerk
                      ));
  }

  public function IPNAction() {
    $listener = new Listener;
    $verifier = new CurlVerifier;
    $ipnMessage = Message::createFromGlobals(); // uses php://input

    $verifier->setIpnMessage($ipnMessage);
    $verifier->setEnvironment('production'); // can either be sandbox or production

    $listener->setVerifier($verifier);

    $listener->listen(function() use ($listener, $ipnMessage) {
        // on verified IPN (everything is good!)
      $resp = $listener->getVerifier()->getVerificationResponse();
      $amount = $ipnMessage['mc_gross'];
      $smallId = $ipnMessage['custom'];

      if($ipnMessage['payment_status'] == 'Completed') {

        if(is_numeric($smallId)) {
          $user = User::whereSmallId($smallId)->first();
          $user->addDonation($amount);
          $user->save();
        }
      }

      $donationLog = new DonationLog;
      $donationLog->status = $ipnMessage['payment_status'];
      $donationLog->amount = $amount - $ipnMessage['mc_fee '];
      $donationLog->original_amount = $amount;
      if(is_numeric($smallId) && isset($user) && $user->getId()) {
        $donationLog->small_id = $smallId;
      }
      $donationLog->save();

    }, function() use ($listener, $ipnMessage) {
        // on invalid IPN (somethings not right!)
      $report = $listener->getReport();
      $resp = $listener->getVerifier()->getVerificationResponse();
    });
  }

}
