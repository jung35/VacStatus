var Subscription = React.createClass({displayName: "Subscription",

	handleSubmit: function(e)
	{
		e.preventDefault();

		var email = this.refs.subcribeEmail.getDOMNode().value.trim(),
			push_bullet = this.refs.subcribePushBullet.getDOMNode().value.trim();

		if (!email && !push_bullet) {
			notif.add('danger', 'Atleast 1 field needs to be filled out!').run();
			return;
		}

		$.ajax({
			url: '/api/v1/settings/subscribe',
			type: 'POST',
			data: {
				_token: _token,
				email: email,
				push_bullet: push_bullet
			},
			dataType: 'json',
			success: function(data) {
				if(data.error) {
					notif.add('danger', data.error).run();
				} else {
					notif.add('success', 'Settings have been saved!').run();
					this.setState({data: data});
				}
			}.bind(this),
			error: function(xhr, status, err) {
				console.error(this.props.url, status, err.toString());
			}.bind(this)
		});
	},

	emailRemove: function(e)
	{
		e.preventDefault();

		$.ajax({
			url: '/api/v1/settings/subscribe/email',
			type: 'POST',
			data: {
				_token: _token,
				_method: 'DELETE'
			},
			dataType: 'json',
			success: function(data) {
				if(data.error) {
					notif.add('danger', data.error).run();
				} else {
					$('#subcribeEmail').val("");
					notif.add('success', 'Successfully removed email!').add('warning', 'Please check your email to verify!').run();
					this.setState({data: data});
				}
			}.bind(this),
			error: function(xhr, status, err) {
				console.error(this.props.url, status, err.toString());
			}.bind(this)
		});
	},

	pushBulletRemove: function(e)
	{
		e.preventDefault();

		$.ajax({
			url: '/api/v1/settings/subscribe/pushbullet',
			type: 'POST',
			data: {
				_token: _token,
				_method: 'DELETE'
			},
			dataType: 'json',
			success: function(data) {
				if(data.error) {
					notif.add('danger', data.error).run();
				} else {
					$('#subcribePushBullet').val("");
					notif.add('success', 'Successfully removed pushbullet!').run();
					this.setState({data: data});
				}
			}.bind(this),
			error: function(xhr, status, err) {
				console.error(this.props.url, status, err.toString());
			}.bind(this)
		});
	},

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
			emailInputSmall,
			emailButton,
			emailColor,
			pushBulletInputSmall,
			pushBulletButton,
			pushBulletColor,
			userList;

		data = this.state.data;
		
		if(data !== null)
		{
			emailInputSmall = false;
			pushBulletInputSmall = false;

			if(data.userMail != null)
			{
				if(data.userMail.email)
				{
					emailInputSmall = true;
					emailColor = "has-warning";

					emailButton = (
						React.createElement("div", {className: "col-sm-2"}, 
							React.createElement("button", {type: "button", onClick: this.emailRemove, className: "btn btn-block btn-danger"}, "Remove")
						)
					);

					if(data.userMail.verify == "verified")
					{
						emailColor = "has-success";


					}
				}

				if(data.userMail.pushbullet)
				{
					pushBulletInputSmall = true;
					pushBulletColor = "has-warning";

					pushBulletButton = (
						React.createElement("div", {className: "col-sm-2"}, 
							React.createElement("button", {type: "button", onClick: this.pushBulletRemove, className: "btn btn-block btn-danger"}, "Remove")
						)
					);


					if(data.userMail.pushbullet_verify == "verified")
					{
						pushBulletColor = "has-success";
					}
				}
			}
			userLists = React.createElement("div", {className: "col-xs-12"}, React.createElement("i", null, "You're not subscribed to any list."));

			if(data.userLists !== null && data.userLists !== undefined)
			{
				userLists = data.userLists.map(function(list, index)
				{
					var specialColors = "";
					if(list.beta) specialColors = "beta-name";
					if(list.donation >= 10.0) specialColors = "donator-name";
					if(list.site_admin >= 1) specialColors = "admin-name";

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
									React.createElement("form", {onSubmit: this.handleSubmit, className: "subscribe-form form-horizontal"}, 
										React.createElement("div", {className: "form-group " + emailColor}, 
											React.createElement("label", {htmlFor: "subcribeEmail", className: "col-sm-2 control-label"}, "Email"), 
											React.createElement("div", {className:  emailInputSmall ? "col-sm-8 " : "col-sm-10"}, 
												React.createElement("input", {type: "email", className: "form-control", id: "subcribeEmail", ref: "subcribeEmail", placeholder: "Email", defaultValue:  data.userMail ? data.userMail.email:''})
											), 
											emailButton 
										), 
										React.createElement("div", {className: "form-group " + pushBulletColor}, 
											React.createElement("label", {htmlFor: "subcribePushBullet", className: "col-sm-2 control-label"}, "Pushbullet"), 
											React.createElement("div", {className:  pushBulletInputSmall ? "col-sm-8" : "col-sm-10"}, 
												React.createElement("input", {type: "email", className: "form-control", id: "subcribePushBullet", ref: "subcribePushBullet", placeholder: "PushBullet Email", defaultValue:  data.userMail ? data.userMail.pushbullet:''})
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