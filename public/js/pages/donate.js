var auth_check = $('meta[name=auth]').attr("content");

var Donate = React.createClass({displayName: "Donate",

	fetchDonate: function()
	{
		$.ajax({
			url: '/api/v1/donate',
			dataType: 'json',
			success: function(data) {
				this.setState({data: data});
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
		return {
			data: null
		};
	},

	render: function()
	{
		var data, small_id, url, payAs, donatePerk, recentDonation, topDonation;
		data = this.state.data;
		if(data !== null)
		{
			url = "http://vacstatus.app";
			small_id = "Anonymous";
			payAs = React.createElement("b", null, "You are not logged in!");

			if(auth_check) {
				small_id = data.user.small_id;
				payAs = React.createElement("div", null, "You are Logged in as: ", React.createElement("b", null,  data.user.display_name));
			}

			donatePerk = data.donationPerk.map(function(perk, index)
			{
				return (
			        React.createElement("tr", {key: index }, 
						React.createElement("td", null,  perk.desc), 
						React.createElement("td", null, "$",  perk.amount)
					)
		        );
			});

			recentDonation = data.latestDonation.map(function(donation, index)
			{
				var specialColors, url;

				specialColors = "";
				if(donation.beta) specialColors = "beta-name";
				if(donation.donation >= 10.0) specialColors = "donator-name";
				if(donation.site_admin) specialColors = "admin-name";

				url = "#";
				if(donation.display_name != null)
				{
					url = "/u/" + donation.steam_64_bit;
				}

				return (
					React.createElement("tr", {key: index }, 
						React.createElement("td", {className: "text-center"},  index + 1), 
						React.createElement("td", null, 
							React.createElement("a", {href: url, className: specialColors }, 
								 donation.display_name == null ? 'Anonymous': donation.display_name
							)
						), 
						React.createElement("td", {className: "text-center"}, "$",  donation.original_amount)
					)
		        );
			});

			topDonation = data.mostDonation.map(function(donation, index)
			{
				var specialColors;

				specialColors = "";
				if(donation.beta) specialColors = "beta-name";
				if(donation.donation >= 10.0) specialColors = "donator-name";
				if(donation.site_admin) specialColors = "admin-name";

				return (
					React.createElement("tr", {key: index }, 
						React.createElement("td", {className: "text-center"},  index + 1), 
						React.createElement("td", null, 
							React.createElement("a", {href: "/u/" + donation.steam_64_bit, className: specialColors }, 
								 donation.display_name
							)
						), 
						React.createElement("td", {className: "text-center"}, "$",  donation.donation.toFixed(2) )
					)
		        );
			});

			return (
				React.createElement("div", {className: "container"}, 
					React.createElement("div", {className: "row"}, 
						React.createElement("div", {className: "col-xs-12 col-lg-10 col-lg-offset-1"}, 
							React.createElement("h1", null, "Donations"), 
							React.createElement("div", {className: "row"}, 
								React.createElement("div", {className: "col-xs-12 col-md-6"}, 
									React.createElement("h3", null, "Why Donate?"), 
									React.createElement("p", null, "VacStatus was created as a hobby and will forever be free to use.", 
									React.createElement("br", null), "But in order to maintain this website, it costs time and money.", 
									React.createElement("br", null), "If you enjoy using this service, please feel free to donate."), 
									React.createElement("form", {action: "https://www.paypal.com/cgi-bin/webscr", method: "post"}, 
										React.createElement("input", {type: "hidden", name: "cmd", value: "_donations"}), 
										React.createElement("input", {type: "hidden", name: "item_name", value: "Donation"}), 
										React.createElement("input", {type: "hidden", name: "business", value: "jung3o@yahoo.com"}), 
										React.createElement("input", {type: "hidden", name: "notify_url", value:  url + "/api/v1/donate/ipn"}), 
										React.createElement("input", {type: "hidden", name: "return", value: "{{ url('/donate') }}"}), 
										React.createElement("input", {type: "hidden", name: "rm", value: "2"}), 
										React.createElement("input", {type: "hidden", name: "custom", value: small_id }), 
										React.createElement("input", {type: "hidden", name: "no_note", value: "1"}), 
										React.createElement("input", {type: "hidden", name: "cbt", value: "Go Back To The Site"}), 
										React.createElement("input", {type: "hidden", name: "no_shipping", value: "1"}), 
										React.createElement("input", {type: "hidden", name: "lc", value: "US"}), 
										React.createElement("input", {type: "hidden", name: "currency_code", value: "USD"}), 
										React.createElement("input", {type: "hidden", name: "bn", value: "PP-DonationsBF:btn_donate_LG.gif:NonHostedGuest"}), 
										React.createElement("div", {className: "row"}, 
											React.createElement("div", {className: "col-xs-12 col-sm-6 col-md-3 donate-button"}, 
												React.createElement("input", {type: "image", src: "https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif", name: "submit", alt: "PayPal - The safer, easier way to pay online!"})
											), 
											React.createElement("div", {className: "col-xs-12 col-sm-6 col-md-9 donate-user-text"}, 
												React.createElement("label", null, payAs )
											)
										)
									), 
									React.createElement("br", null), 
									React.createElement("h3", null, "Where does donations go?"), 
									React.createElement("p", null, "The priority is on the website.", 
									React.createElement("br", null), "All of the donations will go on making VacStatus a better service.")
								), 
								React.createElement("div", {className: "col-xs-12 col-md-6"}, 
									React.createElement("h3", null, "Thank you!"), 
									React.createElement("p", null, "Following are some \"perks\" you will recieve when you donate.", 
									React.createElement("br", null), "If you donate more than some of the \"perks\" listed, *", 
									React.createElement("br", null), "you will receive the ones below that donation. **"), 
									React.createElement("table", {className: "table"}, 
										React.createElement("thead", null, 
											React.createElement("tr", null, 
												React.createElement("th", null, "Perk"), 
												React.createElement("th", {width: "90px"}, "Amount")
											)
										), 
										React.createElement("tbody", null, 
											donatePerk 
										)
									), 
									React.createElement("p", {className: "tiny-text"}, "* Donations do add up."), 
									React.createElement("p", {className: "tiny-text"}, "** You can also donate as guest by logging off, but no perks will be given.")
								)
							), 
							React.createElement("div", {className: "row"}, 
								React.createElement("div", {className: "col-xs-12 col-md-6"}, 
									React.createElement("h3", null, "Recent Donations"), 
									React.createElement("table", {className: "table"}, 
										React.createElement("thead", null, 
											React.createElement("tr", null, 
												React.createElement("th", {width: "40px", className: "text-center"}), 
												React.createElement("th", null, "Username"), 
												React.createElement("th", {width: "90px"}, React.createElement("div", {className: "text-center"}, "Amount"))
											)
										), 
										React.createElement("tbody", null, 
											recentDonation 
										)
									)
								), 
								React.createElement("div", {className: "col-xs-12 col-md-6"}, 
									React.createElement("h3", null, "Top Donations"), 
									React.createElement("table", {className: "table"}, 
										React.createElement("thead", null, 
											React.createElement("tr", null, 
												React.createElement("th", {width: "40px", className: "text-center"}), 
												React.createElement("th", null, "Username"), 
												React.createElement("th", {width: "90px"}, React.createElement("div", {className: "text-center"}, "Amount"))
											)
										), 
										React.createElement("tbody", null, 
											topDonation 
										)
									)
								)
							)
						)
					)
				)
	        );
		}

		return React.createElement("div", null);
	}
});

React.render(React.createElement(Donate, null), document.getElementById('donate'))