var steam64BitId = $('#profile').data('steam64bitid');

var Profile = React.createClass({displayName: "Profile",
	fetchProfile: function(steam64BitId)
	{
		$.ajax({
			url: '/api/v1/profile/'+steam64BitId,
			dataType: 'json',
			success: function(data) {
				this.setState({data: data});
			}.bind(this),
			error: function(xhr, status, err) {
				console.error(this.props.url, status, err.toString());
			}.bind(this)
		});
	},
	componentDidMount: function() {
		this.fetchProfile(steam64BitId);
	},
	getInitialState: function() {
		return {
			data: null
		};
	},
	render: function()
	{
		var data, specialColors, auth, privacy, alias_history, alias_recent;

		data = this.state.data;

		if(data != null)
		{
			if(data.beta) specialColors = "beta";
			if(data.donation >= 10.0) specialColors = "donator";
			if(data.site_admin) specialColors = "admin";

			if(data.login_check) auth = React.createElement("a", {href: "#"}, React.createElement("span", {className: "fa fa-plus faText-align"}));

			switch(data.privacy)
			{
				case 3:
					privacy = {
						type: "Public",
						color: "primary"
					};
					break;
				case 2:
					privacy = {
						type: "Friends Only",
						color: "warning"
					};
					break;
				default:
					privacy = {
						type: "Private",
						color: "danger"
					};
					break;
			}

			alias_history = data.profile_old_alias.map(function(alias, index) {
				return (
					React.createElement("tr", {key: index}, 
						React.createElement("td", null,  alias.timechanged), 
						React.createElement("td", null,  alias.newname)
					)
		        );
			});

			alias_recent = data.alias.map(function(alias, index) {
				return (
					React.createElement("tr", {key: index}, 
						React.createElement("td", null,  alias.timechanged.replace('@', '') ), 
						React.createElement("td", null,  alias.newname)
					)
		        );
			});

			return (
				React.createElement("div", {className: "profile-start"}, 
					React.createElement("div", {className: "profile-header"}, 
						React.createElement("div", {className: "container"}, 
							React.createElement("div", {className: "row"}, 
								React.createElement("div", {className: "col-xs-12 col-md-3 col-lg-2 col-lg-offset-1"}, 
									React.createElement("div", {className: "profile-avatar"}, 
										React.createElement("img", {className: "img-responsive", src:  data.avatar})
									)
								), 
								React.createElement("div", {className: "col-xs-12 col-md-9"}, 
									React.createElement("div", {className: "row"}, 
										React.createElement("div", {className: "col-xs-12"}, 
											React.createElement("div", {className: "profile-username"}, 
												auth, 
												React.createElement("span", {className:  specialColors + "-name"}, " ",  data.display_name)
											)
										)
									), 
									React.createElement("div", {className: "row"}, 
										React.createElement("div", {className: "col-xs-12 col-md-2"}, 
											React.createElement("div", {className: "profile-steam"}, 
												React.createElement("a", {href: "http://steamcommunity.com/profiles/" + data.steam_64_bit, target: "_blank"}, 
													React.createElement("span", {className: "fa fa-steam"})
												)
											)
										), 
										React.createElement("div", {className: "col-xs-12 col-sm-6 col-md-4"}, 
											React.createElement("ul", {className: "profile-info"}, 
												React.createElement("li", null, 
													React.createElement("div", {className: "row"}, 
														React.createElement("div", {className: "col-xs-6 text-right"}, React.createElement("strong", null, "Creation")), 
														React.createElement("div", {className: "col-xs-6"},  data.profile_created)
													)
												), 
												React.createElement("li", null, 
													React.createElement("div", {className: "row"}, 
														React.createElement("div", {className: "col-xs-6 text-right"}, React.createElement("strong", null, "Steam3 ID")), 
														React.createElement("div", {className: "col-xs-6"}, "U:1:" + data.small_id)
													)
												), 
												React.createElement("li", null, 
													React.createElement("div", {className: "row"}, 
														React.createElement("div", {className: "col-xs-6 text-right"}, React.createElement("strong", null, "Steam ID 32")), 
														React.createElement("div", {className: "col-xs-6"},  data.steam_32_bit)
													)
												), 
												React.createElement("li", null, 
													React.createElement("div", {className: "row"}, 
														React.createElement("div", {className: "col-xs-6 text-right"}, React.createElement("strong", null, "Steam ID 64")), 
														React.createElement("div", {className: "col-xs-6"},  data.steam_64_bit)
													)
												)
											)
										), 
										React.createElement("div", {className: "col-xs-12 col-sm-6 col-md-6 col-lg-5"}, 
											React.createElement("ul", {className: "profile-info"}, 
												React.createElement("li", null, 
													React.createElement("div", {className: "row"}, 
														React.createElement("div", {className: "col-xs-6 text-right"}, React.createElement("strong", null, "Profile Status")), 
														React.createElement("div", {className: "col-xs-6"}, React.createElement("div", {className: "text-" + privacy.color},  privacy.type))
													)
												), 
												React.createElement("li", null, 
													React.createElement("div", {className: "row"}, 
														React.createElement("div", {className: "col-xs-6 text-right"}, React.createElement("strong", null, "Vac Ban")), 
														React.createElement("div", {className: "col-xs-6"}, React.createElement("div", {className: "text-" + (data.vac > 0 ? 'danger' : 'success') },  data.vac > 0 ? data.vac_banned_on : 'Normal'))
													)
												), 
												React.createElement("li", null, 
													React.createElement("div", {className: "row"}, 
														React.createElement("div", {className: "col-xs-6 text-right"}, React.createElement("strong", null, "Trade ban")), 
														React.createElement("div", {className: "col-xs-6"}, React.createElement("div", {className: "text-" + (data.trade ? 'danger' : 'success')},  data.trade ? 'Banned' : 'Normal'))
													)
												), 
												React.createElement("li", null, 
													React.createElement("div", {className: "row"}, 
														React.createElement("div", {className: "col-xs-6 text-right"}, React.createElement("strong", null, "Community Ban")), 
														React.createElement("div", {className: "col-xs-6"}, React.createElement("div", {className: "text-" + (data.community ? 'danger' : 'success')},  data.community ? 'Banned' : 'Normal'))
													)
												)
											)
										)
									)
								)
							)
						)
					), 
					React.createElement("div", {className: "profile-badge"}, 
						React.createElement("div", {className: "container"}, 
							React.createElement("div", {className: "row"}, 
								React.createElement("div", {className: "col-xs-12"}, 
									 data.site_admin ? React.createElement("div", {className: "label label-warning"}, "Admin") : '', 
									 data.donation >= 10 ? React.createElement("div", {className: "label label-success"}, "Donator") : '', 
									 data.beta ? React.createElement("div", {className: "label label-primary"}, "Beta") : ''
								)
							)
						)
					), 
					React.createElement("div", {className: "profile-body"}, 
						React.createElement("div", {className: "container"}, 
							React.createElement("div", {className: "row"}, 
								React.createElement("div", {className: "col-xs-12"}, 
									React.createElement("div", {className: "title"}, 
										"User Aliases"
									)
								), 
								React.createElement("div", {className: "col-xs-12 col-md-6 col-lg-5 col-lg-offset-1"}, 
									React.createElement("div", {className: "table-responsive"}, 
										React.createElement("table", {className: "table"}, 
											React.createElement("thead", null, 
												React.createElement("tr", null, 
													React.createElement("th", {colSpan: "2"}, "Alias History")
												), 
												React.createElement("tr", null, 
													React.createElement("th", null, "Used On"), 
													React.createElement("th", null, "Username")
												)
											), 
											React.createElement("tbody", null, 
												alias_history 
											)
										)
									)
								), 
								React.createElement("div", {className: "col-xs-12 col-md-6 col-lg-5"}, 
									React.createElement("div", {className: "table-responsive"}, 
										React.createElement("table", {className: "table"}, 
											React.createElement("thead", null, 
												React.createElement("tr", null, 
													React.createElement("th", {colSpan: "2"}, "Recent Aliases")
												), 
												React.createElement("tr", null, 
													React.createElement("th", null, "Used On"), 
													React.createElement("th", null, "Username")
												)
											), 
											React.createElement("tbody", null, 
												alias_recent 
											)
										)
									)
								)
							), 
							React.createElement("hr", {className: "divider"}), 
							React.createElement("div", {className: "row"}, 
								React.createElement("div", {className: "col-xs-12 col-md-2 col-md-offset-2"}, 
									React.createElement("h3", {className: "title"}, "Extra Info"), 
									React.createElement("div", {className: "content text-center"}, 
										React.createElement("div", {className: "row"}, 
											React.createElement("div", {className: "col-xs-6 col-md-12"}, 
												React.createElement("strong", null, "# of VAC Bans"), React.createElement("br", null), 
													 data.vac
											)
										)
									)
								), 
								React.createElement("div", {className: "col-xs-12 col-md-6"}, 
									React.createElement("h3", {className: "title"}, "VacStatus Info"), 
									React.createElement("div", {className: "content text-center"}, 
										React.createElement("div", {className: "row"}, 
											React.createElement("div", {className: "col-xs-6 col-md-4"}, 
												React.createElement("strong", null, "First Checked"), React.createElement("br", null), 
													 data.created_at
											), 
											React.createElement("div", {className: "col-xs-6 col-md-4"}, 
												React.createElement("strong", null, "Times Checked"), React.createElement("br", null), 
													 data.times_checked.number, " ", React.createElement("sub", null,  data.times_checked.number ? "(" + data.times_checked.time + ")" : '')
											), 
											React.createElement("div", {className: "col-xs-12 col-md-4"}, 
												React.createElement("strong", null, "Times Added"), React.createElement("br", null), 
													 data.times_added.number, " ", React.createElement("sub", null,  data.times_added.number ? "(" + data.times_added.time + ")" : '')
											)
										)
									)
								)
							), 
							React.createElement("hr", {className: "divider"})
						)
					)
				)
			);
		} else {
			return (
	      		React.createElement("div", null)
	        )
		}
	}
});

React.render(React.createElement(Profile, null), document.getElementById('profile'));