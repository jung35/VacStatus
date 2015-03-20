var Subscription = React.createClass({

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
						<div className="col-sm-2">
							<button className="btn btn-block btn-info">Resend</button>
						</div>
					);

					if(data.userMail.verify == "verified")
					{
						emailColor = "has-success";

						emailButton = (
							<div className="col-sm-2">
								<button className="btn btn-block btn-danger">Remove</button>
							</div>
						);

						emailVerified = true;
					}
				}

				if(data.userMail.pushbullet !== null && data.userMail.pushbullet !== undefined)
				{
					pushBulletColor = "has-warning";

					pushBulletButton = (
						<div className="col-sm-2">
							<button className="btn btn-block btn-info">Resend</button>
						</div>
					);

					if(data.userMail.pushbullet_verify == "verified")
					{
						pushBulletColor = "has-success";

						pushBulletButton = (
							<div className="col-sm-2">
								<button className="btn btn-block btn-danger">Remove</button>
							</div>
						);

						pushBulletVerified = true;
					}
				}
			}
			userLists = <div className="col-xs-12"><i>You're not subscribed to any list.</i></div>;

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
									<form className="subscribe-form form-horizontal">
										<div className={"form-group " + emailColor}>
											<label htmlFor="subcribeEmail" className="col-sm-2 control-label">Email</label>
											<div className={ emailVerified ? "col-sm-8 " : "col-sm-10"}>
												<input type="email" className="form-control" id="subcribeEmail" ref="subcribeEmail" placeholder="Email" defaultValue={ data.userMail.email } />
											</div>
											{ emailButton }
										</div>
										<div className={"form-group " + pushBulletColor}>
											<label htmlFor="subcribePushBullet" className="col-sm-2 control-label">Pushbullet</label>
											<div className={ pushBulletVerified ? "col-sm-8" : "col-sm-10"}>
												<input type="email" className="form-control" id="subcribePushBullet" ref="subcribePushBullet" placeholder="PushBullet Email" defaultValue={ data.userMail.pushbullet } />
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