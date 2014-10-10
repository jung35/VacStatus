<?php
use PayPal\Ipn\Listener;
use PayPal\Ipn\Message;
use PayPal\Ipn\Verifier\CurlVerifier;

class DonationController extends \BaseController {

  public function DonationAction() {

    $latestDonation = DonationLog::

    return View::make('donation/donation');
  }

  public function IPNAction() {
    $listener = new Listener;
    $verifier = new CurlVerifier;
    $ipnMessage = Message::createFromGlobals(); // uses php://input

    $verifier->setIpnMessage($ipnMessage);
    $verifier->setEnvironment('sandbox'); // can either be sandbox or production

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
