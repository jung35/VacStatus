var Subscription = React.createClass({

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
						<div className="col-sm-2">
							<button type="button" onClick={this.emailRemove} className="btn btn-block btn-danger">Remove</button>
						</div>
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
						<div className="col-sm-2">
							<button type="button" onClick={this.pushBulletRemove} className="btn btn-block btn-danger">Remove</button>
						</div>
					);


					if(data.userMail.pushbullet_verify == "verified")
					{
						pushBulletColor = "has-success";
					}
				}
			}
			userLists = <div className="col-xs-12"><i>You're not subscribed to any list.</i></div>;

			if(data.userLists !== null && data.userLists !== undefined)
			{
				userLists = data.userLists.map(function(list, index)
				{
					var specialColors = "";
					if(list.beta) specialColors = "beta-name";
					if(list.donation >= 10.0) specialColors = "donator-name";
					if(list.site_admin) specialColors = "admin-name";

					return (
						<div key={ index } className="col-xs-6 col-sm-4">
							<a href={"/list/" + list.id}>
								<div className="panel panel-default">
									<div className="panel-body">
										<div className="list-name">{ list.title }</div>
										<div className={"list-author " + specialColors}>{ list.display_name }</div>
									</div>
								</div>
							</a>
						</div>
					);
				})
			}

			return (
				<div className="container">
					<div className="row">
						<div className="col-xs-12">
							<h1>Settings</h1>
							<div className="row">
								<div className="col-xs-12 col-md-6">
									<h3>Receive Updates <small>&mdash; You only need to enter in one of them</small></h3>
									<form onSubmit={this.handleSubmit} className="subscribe-form form-horizontal">
										<div className={"form-group " + emailColor}>
											<label htmlFor="subcribeEmail" className="col-sm-2 control-label">Email</label>
											<div className={ emailInputSmall ? "col-sm-8 " : "col-sm-10"}>
												<input type="email" className="form-control" id="subcribeEmail" ref="subcribeEmail" placeholder="Email" defaultValue={ data.userMail ? data.userMail.email:'' } />
											</div>
											{ emailButton }
										</div>
										<div className={"form-group " + pushBulletColor}>
											<label htmlFor="subcribePushBullet" className="col-sm-2 control-label">Pushbullet</label>
											<div className={ pushBulletInputSmall ? "col-sm-8" : "col-sm-10"}>
												<input type="email" className="form-control" id="subcribePushBullet" ref="subcribePushBullet" placeholder="PushBullet Email" defaultValue={ data.userMail ? data.userMail.pushbullet:'' } />
											</div>
											{ pushBulletButton }
										</div>
										<div className="form-group">
											<div className="col-sm-offset-2 col-sm-10">
												<button className="btn btn-block btn-primary">Save Settings</button>
											</div>
										</div>
									</form>			
								</div>
								<div className="col-xs-12 col-md-6">
									<h3>Subscribed Lists <small>&mdash; You need to subscribe a list to receive notification</small></h3>
									<div className="subscribed-list">
										<div className="row">
											{ userLists }
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			);
		}
		return <div></div>;
	}
});

React.render(<Subscription />, document.getElementById('subscription'));