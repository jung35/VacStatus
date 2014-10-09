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

    $listener->listen(function() use ($listener) {
        // on verified IPN (everything is good!)
      $resp = $listener->getVerifier()->getVerificationResponse();
      var_dump(true,$resp);
      dd();
    }, function() use ($listener) {
        // on invalid IPN (somethings not right!)
      $report = $listener->getReport();
      $resp = $listener->getVerifier()->getVerificationResponse();
      var_dump(false,$resp, $report);
      print_r($report);
      dd();
    });
  }

}
