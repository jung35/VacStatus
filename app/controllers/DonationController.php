<?php
use PayPal\Ipn\Listener;
use PayPal\Ipn\Message;
use PayPal\Ipn\Verifier\CurlVerifier;

class DonationController extends \BaseController {

  public function DonationAction() {

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
      Log::info('stuff123', (array) $ipnMessage);
      dd();
    }, function() use ($listener, $ipnMessage) {
        // on invalid IPN (somethings not right!)
      $report = $listener->getReport();
      $resp = $listener->getVerifier()->getVerificationResponse();
      Log::info('stuff boo', (array) $ipnMessage);
      dd();
    });
  }

}
