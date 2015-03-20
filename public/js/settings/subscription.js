var Subscription = React.createClass({displayName: "Subscription",

	fetchSubscription: function()
	{
		$.ajax({
			url: '/api/v1/settings',
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
		this.fetchSubscription();
	},

	getInitialState: function()
	{
		return {
			data: null
		};
	},
	
	render:  function()
	{
		var data,
			emailVerified,
			emailButton,
			emailColor,
			pushBulletVerified,
			pushBulletButton,
			pushBulletColor,
			userList;

		data = this.state.data;
		
		if(data !== null)
		{
			emailVerified = false;
			pushBulletVerified = false;

			if(data.userMail !== null)
			{
				if(data.userMail.email !== null && data.userMail.email !== undefined)
				{
					emailColor = "has-warning";

					emailButton = (
						React.createElement("div", {className: "col-sm-2"}, 
							React.createElement("button", {className: "btn btn-block btn-info"}, "Resend")
						)
					);

					if(data.userMail.verify == "verified")
					{
						emailColor = "has-success";

						emailButton = (
							React.createElement("div", {className: "col-sm-2"}, 
								React.createElement("button", {className: "btn btn-block btn-danger"}, "Remove")
							)
						);

						emailVerified = true;
					}
				}

				if(data.userMail.pushbullet !== null && data.userMail.pushbullet !== undefined)
				{
					pushBulletColor = "has-warning";

					pushBulletButton = (
						React.createElement("div", {className: "col-sm-2"}, 
							React.createElement("button", {className: "btn btn-block btn-info"}, "Resend")
						)
					);

					if(data.userMail.pushbullet_verify == "verified")
					{
						pushBulletColor = "has-success";

						pushBulletButton = (
							React.createElement("div", {className: "col-sm-2"}, 
								React.createElement("button", {className: "btn btn-block btn-danger"}, "Remove")
							)
						);

						pushBulletVerified = true;
					}
				}
			}
			userLists = React.createElement("div", {className: "col-xs-12"}, React.createElement("i", null, "You're not subscribed to any list."));

			console.log(data.userLists);
			if(data.userLists !== null && data.userLists !== undefined)
			{
				userLists = data.userLists.map(function(list, index)
				{
					var specialColors = "";
					if(list.beta) specialColors = "beta-name";
					if(list.donation >= 10.0) specialColors = "donator-name";
					if(list.site_admin) specialColors = "admin-name";

					return (
						React.createElement("div", {key: index, className: "col-xs-6 col-sm-4"}, 
							React.createElement("a", {href: "/list/" + list.id}, 
								React.createElement("div", {className: "panel panel-default"}, 
									React.createElement("div", {className: "panel-body"}, 
										React.createElement("div", {className: "list-name"},  list.title), 
										React.createElement("div", {className: "list-author " + specialColors},  list.display_name)
									)
								)
							)
						)
					);
				})
			}

			return (
				React.createElement("div", {className: "container"}, 
					React.createElement("div", {className: "row"}, 
						React.createElement("div", {className: "col-xs-12"}, 
							React.createElement("h1", null, "Settings"), 
							React.createElement("div", {className: "row"}, 
								React.createElement("div", {className: "col-xs-12 col-md-6"}, 
									React.createElement("h3", null, "Receive Updates ", React.createElement("small", null, "— You only need to enter in one of them")), 
									React.createElement("form", {className: "subscribe-form form-horizontal"}, 
										React.createElement("div", {className: "form-group " + emailColor}, 
											React.createElement("label", {htmlFor: "subcribeEmail", className: "col-sm-2 control-label"}, "Email"), 
											React.createElement("div", {className:  emailVerified ? "col-sm-8 " : "col-sm-10"}, 
												React.createElement("input", {type: "email", className: "form-control", id: "subcribeEmail", ref: "subcribeEmail", placeholder: "Email", defaultValue:  data.userMail.email})
											), 
											emailButton 
										), 
										React.createElement("div", {className: "form-group " + pushBulletColor}, 
											React.createElement("label", {htmlFor: "subcribePushBullet", className: "col-sm-2 control-label"}, "Pushbullet"), 
											React.createElement("div", {className:  pushBulletVerified ? "col-sm-8" : "col-sm-10"}, 
												React.createElement("input", {type: "email", className: "form-control", id: "subcribePushBullet", ref: "subcribePushBullet", placeholder: "PushBullet Email", defaultValue:  data.userMail.pushbullet})
											), 
											pushBulletButton 
										), 
										React.createElement("div", {className: "form-group"}, 
											React.createElement("div", {className: "col-sm-offset-2 col-sm-10"}, 
												React.createElement("button", {className: "btn btn-block btn-primary"}, "Save Settings")
											)
										)
									)			
								), 
								React.createElement("div", {className: "col-xs-12 col-md-6"}, 
									React.createElement("h3", null, "Subscribed Lists ", React.createElement("small", null, "— You need to subscribe a list to receive notification")), 
									React.createElement("div", {className: "subscribed-list"}, 
										React.createElement("div", {className: "row"}, 
											userLists 
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

React.render(React.createElement(Subscription, null), document.getElementById('subscription'));