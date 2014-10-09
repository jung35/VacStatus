@extends('layout')

@section('content')

<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
  <input type="hidden" name="cmd" value="_donations">
  <input type="hidden" name="item_name" value="Donation">
  <input type="hidden" name="business" value="jung3o-facilitator@yahoo.com">
  <input type="hidden" name="notify_url" value="http://test.vacstatus.com/ipn">
  <input type="hidden" name="return" value="http://test.vacstatus.com/donation">
  <input type="hidden" name="rm" value="2">
  <input type="hidden" name="custom" value="123123">
  <input type="hidden" name="no_note" value="1">
  <input type="hidden" name="cbt" value="Go Back To The Site">
  <input type="hidden" name="no_shipping" value="1">
  <input type="hidden" name="lc" value="US">
  <input type="hidden" name="currency_code" value="USD">
  <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest">
  <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!">
  <img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

@stop
