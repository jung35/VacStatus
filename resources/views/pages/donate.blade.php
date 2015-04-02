@extends('layout.app')

@section('content')
<div id="donate" class="donate-page">
{{-- 	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-lg-10 col-lg-offset-1">
				<h2>Donations</h2>
				<div class="row">
					<div class="col-xs-12 col-md-6">
						<h4>Why Donate?</h4>
						<p style="line-height: 170%; font-size: 14px">VacStatus was created as a hobby and will forever be free to use.
						<br>But in order to maintain this website, it costs time and money.
						<br>If you enjoy using this service, please feel free to donate.</p>
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
							<input type="hidden" name="cmd" value="_donations">
							<input type="hidden" name="item_name" value="Donation">
							<input type="hidden" name="business" value="jung3o@yahoo.com">
							<input type="hidden" name="notify_url" value="{{ url('/api/v1/donate/ipn') }}">
							<input type="hidden" name="return" value="{{ url('/donate') }}">
							<input type="hidden" name="rm" value="2">
							<input type="hidden" name="custom" value="{{{ Auth::check() ? Auth::User()->small_id : "Anonymous" }}}">
							<input type="hidden" name="no_note" value="1">
							<input type="hidden" name="cbt" value="Go Back To The Site">
							<input type="hidden" name="no_shipping" value="1">
							<input type="hidden" name="lc" value="US">
							<input type="hidden" name="currency_code" value="USD">
							<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-3 donate-button">
									<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!">
								</div>
								<div class="col-xs-12 col-sm-6 col-md-9 donate-user-text">
									<label style="line-height: 25px;">
										@if(Auth::check())
											You are Logged in as: <b>{{{ Auth::User()->display_name }}}</b>
										@else
											<b>You are not logged in!</b>
										@endif
									</label>
								</div>
							</div>
						</form>
						<br>
						<h4>Where does donations go?</h4>
						<p style="line-height: 170%; font-size: 14px">The priority is on the website.
						<br>All of the donations will go on making VacStatus a better service.</p>
					</div>
					<div class="col-xs-12 col-md-6">
						<h4>Thank you!</h4>
						<p style="line-height: 170%; font-size: 14px">Following are some "perks" you will recieve when you donate.
						<br>If you donate more than some of the "perks" listed, *
						<br>you will receive the ones below that donation. **</p>
						<table class="table">
							<thead>
								<tr>
									<th>Perk</th>
									<th width="90px">Amount</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
						<p style="line-height: 5px; font-size: 12px">* Donations do add up.</p>
						<p style="line-height: 5px; font-size: 12px">** You can also donate as guest by logging off, but no perks will be given.</p>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-6">
						<h4>Recent Donations</h4>
						<table class="table">
							<thead>
								<tr>
									<th width="40px" class="text-center"></th>
									<th>Username</th>
									<th width="90px"><div class="text-center">Amount</div></th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
					<div class="col-xs-12 col-md-6">
						<h4>Top Donations</h4>
						<table class="table">
							<thead>
								<tr>
									<th width="40px" class="text-center"></th>
									<th>Username</th>
									<th width="90px"><div class="text-center">Amount</div></th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div> --}}
</div>
<div id="listHandler"></div>
@stop

@section('js')
	<script src="/js/pages/home.js"></script>
	<script src="/js/pages/donate.js"></script>
@stop