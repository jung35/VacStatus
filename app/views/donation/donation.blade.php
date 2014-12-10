@extends('layout')

@section('content')

<div class="large-12 columns">
  <h2>Donations</h2>
  <div class="row">
    <div class="medium-6 columns">
      <h4>Why Donate?</h4>
      <p style="line-height:200%; font-size: 14px">VacStatus was created as a hobby and will forever be free to use.
      <br>But in order to maintain this website, it costs time and money.
      <br>If you enjoy using this service, please feel free to donate.</p>
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_donations">
        <input type="hidden" name="item_name" value="Donation">
        <input type="hidden" name="business" value="jung3o@yahoo.com">
        <input type="hidden" name="notify_url" value="http://vacstat.us/ipn">
        <input type="hidden" name="return" value="http://vacstat.us/donation">
        <input type="hidden" name="rm" value="2">
        <input type="hidden" name="custom" value="{{{ Auth::check() ? Auth::User()->getSmallId() : "Anonymous" }}}">
        <input type="hidden" name="no_note" value="1">
        <input type="hidden" name="cbt" value="Go Back To The Site">
        <input type="hidden" name="no_shipping" value="1">
        <input type="hidden" name="lc" value="US">
        <input type="hidden" name="currency_code" value="USD">
        <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest">
        <div class="row">
          <div class="large-3 medium-3 small-3 columns">
            <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!">
          </div>
          <div class="large-9 medium-9 small-9  columns">
            <label style="line-height: 25px;">
              @if(Auth::check())
                You are Logged in as: <b>{{{ Auth::User()->getUserName() }}}</b>
              @else
                <b>You are not logged in!</b>
              @endif
            </label>
          </div>
        </div>
      </form>
      <br>
      <h4>Where does donations go?<h4>
      <p style="line-height:200%; font-size: 14px">The priority is on the website.
      <br>All of the donations will go on making VacStatus a better service.</p>
    </div>
    <div class="medium-6 columns">
      <h4>As a Thank you.</h4>
      <p style="line-height:200%; font-size: 14px">Following are some "perks" you will recieve when you donate.
      <br>If you donate more than some of the "perks" listed, *
      <br>you will receive the ones below that donation. **</p>
      <table width="100%">
        <thead>
          <tr>
            <th>Perk</th>
            <th width="90px">Amount</th>
          </tr>
        </thead>
        <tbody>
        @foreach($donationPerk as $perk)
          <tr>
            <td>{{{ $perk->getDesc() }}}</td>
            <td>${{{ $perk->getAmount() }}}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
      <p style="line-height:0%; font-size: 12px">* Donations do add up.</p>
      <p style="line-height:0%; font-size: 12px">** You can also donate as guest by logging off, but no perks will be given.</p>
    </div>
  </div>
  <div class="row">
    <div class="medium-6 columns">
      <h4>Recent Donations</h4>
      <table width="100%">
        <thead>
          <tr>
            <th width="40px" class="text-center"></th>
            <th>Username</th>
            <th width="90px"><div class="text-center">Amount</div></th>
          </tr>
        </thead>
        <tbody>
        @foreach($latestDonation as $key => $donator)
          <tr>
            <td class="text-center">{{{ $key + 1 }}}</td>
            <td>
              <a href="{{{ $donator->small_id == null ? '#': URL::route('profile', Array('steam3Id'=> Steam\Steam::toBigId($donator->small_id))) }}}">
                <span {{ (is_numeric($donator->donation) && $donator->donation >= DonationPerk::getPerkAmount('green_name')) ? "class='text-success'" : '' }}>
                {{{ $donator->display_name == null ? 'Anonymous': $donator->display_name }}}
                </span>
              </a>
            </td>
            <td class="text-center">${{{ number_format($donator->original_amount, 2, '.', '') }}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="medium-6 columns">
      <h4>Top Donations</h4>
      <table width="100%">
        <thead>
          <tr>
            <th width="40px" class="text-center"></th>
            <th>Username</th>
            <th width="90px"><div class="text-center">Amount</div></th>
          </tr>
        </thead>
        <tbody>
        @foreach($mostDonation as $key => $donator)
          <tr>
            <td class="text-center">{{{ $key + 1 }}}</td>
            <td>
              <a href="{{{ URL::route('profile', Array('steam3Id'=> $donator->getSteam3Id())) }}}">
                <span {{ (is_numeric($donator->donation) && $donator->donation >= DonationPerk::getPerkAmount('green_name')) ? "class='text-success'" : '' }}>
                {{{ $donator->getUserName() }}}
                </span>
              </a>
            </td>
            <td class="text-center">${{{ $donator->getDonation() }}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@stop
