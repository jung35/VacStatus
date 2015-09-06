var Donate = React.createClass({

	fetchDonate: function()
	{
		$.ajax({
			url: '/api/v1/donate',
			dataType: 'json',
			success: function(data) {
				this.setState(data);
			}.bind(this),
			error: function(xhr, status, err) {
				console.error(this.props.url, status, err.toString());
			}.bind(this)
		});
	},

	componentDidMount: function()
	{
		this.fetchDonate();
	},

	getInitialState: function()
	{
		return { donationPerk: [], latestDonation: [], mostDonation: [], user: {}};
	},

	render: function()
	{
		var small_id, url, payAs, donatePerk, recentDonation, topDonation;

		var donationPerk, latestDonation, mostDonation, user;

		donationPerk = this.state.donationPerk;
		latestDonation = this.state.latestDonation;
		mostDonation = this.state.mostDonation;
		user = this.state.user;

		url = "http://vacstatus.app";
		small_id = "Anonymous";
		payAs = <b>You are not logged in!</b>;

		if(auth_check && user !== null) {
			small_id = user.small_id;
			payAs = <div>You are Logged in as: <b>{ user.display_name }</b></div>;
		}

		donatePerk = donationPerk.map(function(perk, index)
		{
			return (
		        <tr key={ index }>
					<td>{ perk.desc }</td>
					<td>${ perk.amount.toFixed(2) }</td>
				</tr>
	        );
		});

		recentDonation = latestDonation.map(function(donation, index)
		{
			var specialColors, url;

			specialColors = "";
			if(donation.beta >= 1) specialColors = "beta-name";
			if(donation.donation >= 10.0) specialColors = "donator-name";
			if(donation.site_admin >= 1) specialColors = "admin-name";

			url = "#";
			if(donation.display_name != null)
			{
				url = "/u/" + donation.steam_64_bit;
			}

			return (
				<tr key={ index }>
					<td className="text-center">{ index + 1 }</td>
					<td>
						<a href={ url } className={ specialColors }>
							{ donation.display_name == null ? 'Anonymous': donation.display_name }
						</a>
					</td>
					<td className="text-center">${ donation.original_amount.toFixed(2) }</td>
				</tr>
	        );
		});

		topDonation = mostDonation.map(function(donation, index)
		{
			var specialColors;

			specialColors = "";
			if(donation.beta >= 1) specialColors = "beta-name";
			if(donation.donation >= 10.0) specialColors = "donator-name";
			if(donation.site_admin >= 1) specialColors = "admin-name";

			return (
				<tr key={ index }>
					<td className="text-center">{ index + 1 }</td>
					<td>
						<a href={"/u/" + donation.steam_64_bit} className={ specialColors }>
							{ donation.display_name }
						</a>
					</td>
					<td className="text-center">${ donation.donation.toFixed(2) }</td>
				</tr>
	        );
		});

		return (
			<div className="container">
				<div className="row">
					<div className="col-xs-12 col-lg-10 col-lg-offset-1">
						<h1>Donations</h1>
						<div className="row">
							<div className="col-xs-12 col-md-6">
								<h3>Why Donate?</h3>
								<p>VacStatus was created as a hobby and will forever be free to use.
								<br />But in order to maintain this website, it costs time and money.
								<br />If you enjoy using this service, please feel free to donate.</p>
								<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
									<input type="hidden" name="cmd" value="_donations" />
									<input type="hidden" name="item_name" value="Donation" />
									<input type="hidden" name="business" value="jung3o@yahoo.com" />
									<input type="hidden" name="notify_url" value="https://vacstat.us/api/v1/donate/ipn" />
									<input type="hidden" name="return" value="https://vacstat.us/donate" />
									<input type="hidden" name="rm" value="2" />
									<input type="hidden" name="custom" value={ small_id } />
									<input type="hidden" name="no_note" value="1" />
									<input type="hidden" name="cbt" value="Go Back To The Site" />
									<input type="hidden" name="no_shipping" value="1" />
									<input type="hidden" name="lc" value="US" />
									<input type="hidden" name="currency_code" value="USD" />
									<input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest" />
									<div className="row">
										<div className="col-xs-12 col-sm-6 col-md-3 donate-button">
											<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" />
										</div>
										<div className="col-xs-12 col-sm-6 col-md-9 donate-user-text">
											<label>{ payAs }</label>
										</div>
									</div>
								</form>
								<br />
								<h3>Where does donations go?</h3>
								<p>The priority is on the website.
								<br />All of the donations will go on making VacStatus a better service.</p>
							</div>
							<div className="col-xs-12 col-md-6">
								<h3>Thank you!</h3>
								<p>Following are some "perks" you will recieve when you donate.
								<br />If you donate more than some of the "perks" listed, *
								<br />you will receive the ones below that donation. **</p>
								<table className="table">
									<thead>
										<tr>
											<th>Perk</th>
											<th width="90px">Amount</th>
										</tr>
									</thead>
									<tbody>
										{ donatePerk }
									</tbody>
								</table>
								<p className="tiny-text">* Donations do add up.</p>
								<p className="tiny-text">** You can also donate as guest by logging off, but no perks will be given.</p>
							</div>
						</div>
						<div className="row">
							<div className="col-xs-12 col-md-6">
								<h3>Recent Donations</h3>
								<table className="table">
									<thead>
										<tr>
											<th width="40px" className="text-center"></th>
											<th>Username</th>
											<th width="90px"><div className="text-center">Amount</div></th>
										</tr>
									</thead>
									<tbody>
										{ recentDonation }
									</tbody>
								</table>
							</div>
							<div className="col-xs-12 col-md-6">
								<h3>Top Donations</h3>
								<table className="table">
									<thead>
										<tr>
											<th width="40px" className="text-center"></th>
											<th>Username</th>
											<th width="90px"><div className="text-center">Amount</div></th>
										</tr>
									</thead>
									<tbody>
										{ topDonation }
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
        );
	}
});