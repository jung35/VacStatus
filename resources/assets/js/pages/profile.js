var steam64BitId = $('#profile').data('steam64bitid');

var Profile = React.createClass({
	fetchProfile: function()
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
	componentDidMount: function()
	{
		this.fetchProfile();
	},

	getInitialState: function()
	{
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
			specialColors = "";
			if(data.beta >= 1) specialColors = "beta";
			if(data.donation >= 10.0) specialColors = "donator";
			if(data.site_admin >= 1) specialColors = "admin";

			if(auth_check) auth = (
				<a className="open-addUserModal" href="#addUserModal" data-toggle="modal" data-id={ data.id }>
					<span className="fa fa-plus faText-align"></span>
				</a>
			);

			switch(data.privacy)
			{
				case 3:
					privacy = {
						type: "Public",
						color: "success"
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

			if(data.profile_old_alias) {
				alias_history = data.profile_old_alias.map(function(alias, index) {
					return (
						<tr key={index}>
							<td>{ alias.timechanged }</td>
							<td>{ alias.newname }</td>
						</tr>
					);
				});
			} 

			if(data.alias) {
				alias_recent = data.alias.map(function(alias, index) {
					return (
						<tr key={index}>
							<td>{ alias.timechanged }</td>
							<td>{ alias.newname }</td>
						</tr>
					);
				});
			}

			return (
				<div className="profile-start">
					<div className="profile-header">
						<div className="container">
							<div className="row">
								<div className="col-xs-12 col-md-3 col-lg-2 col-lg-offset-1">
									<div className="profile-avatar">
										<img className="img-responsive" src={ data.avatar } />
									</div>
								</div>
								<div className="col-xs-12 col-md-9">
									<div className="row">
										<div className="col-xs-12">
											<div className="profile-username">
												{ auth }
												<span className={ specialColors + "-name"}> { data.display_name }</span>
											</div>
										</div>
									</div>
									<div className="row">
										<div className="col-xs-12 col-md-2">
											<div className="profile-steam">
												<a href={"http://steamcommunity.com/profiles/" + data.steam_64_bit} target="_blank">
													<span className="fa fa-steam"></span>
												</a>
											</div>
										</div>
										<div className="col-xs-12 col-sm-6 col-md-4">
											<ul className="profile-info">
												<li>
													<div className="row">
														<div className="col-xs-6 text-right"><strong>Creation</strong></div>
														<div className="col-xs-6">{ data.profile_created }</div>
													</div>
												</li>
												<li>
													<div className="row">
														<div className="col-xs-6 text-right"><strong>Steam3 ID</strong></div>
														<div className="col-xs-6">{"U:1:" + data.small_id }</div>
													</div>
												</li>
												<li>
													<div className="row">
														<div className="col-xs-6 text-right"><strong>Steam ID 32</strong></div>
														<div className="col-xs-6">{ data.steam_32_bit }</div>
													</div>
												</li>
												<li>
													<div className="row">
														<div className="col-xs-6 text-right"><strong>Steam ID 64</strong></div>
														<div className="col-xs-6">{ data.steam_64_bit }</div>
													</div>
												</li>
											</ul>
										</div>
										<div className="col-xs-12 col-sm-6 col-md-6 col-lg-5">
											<ul className="profile-info">
												<li>
													<div className="row">
														<div className="col-xs-6 text-right"><strong>Profile Status</strong></div>
														<div className="col-xs-6"><div className={"text-" + privacy.color}>{ privacy.type }</div></div>
													</div>
												</li>
												<li>
													<div className="row">
														<div className="col-xs-6 text-right"><strong>VAC / Game Ban</strong></div>
														<div className="col-xs-6">
															<div className={"text-" + (data.vac_bans > 0 || data.game_bans > 0 ? 'danger' : 'success') }>
																{ data.vac_bans > 0 || data.game_bans > 0 ? data.last_ban_date : 'Normal'}
															</div>
														</div>
													</div>
												</li>
												<li>
													<div className="row">
														<div className="col-xs-6 text-right"><strong>Trade ban</strong></div>
														<div className="col-xs-6"><div className={"text-" + (data.trade ? 'danger' : 'success')}>{ data.trade ? 'Banned' : 'Normal' }</div></div>
													</div>
												</li>
												<li>
													<div className="row">
														<div className="col-xs-6 text-right"><strong>Community Ban</strong></div>
														<div className="col-xs-6"><div className={"text-" + (data.community ? 'danger' : 'success')}>{ data.community ? 'Banned' : 'Normal' }</div></div>
													</div>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div className="profile-badge">
						<div className="container">
							<div className="row">
								<div className="col-xs-12">
									{ data.site_admin >= 1 ? <div className="label label-warning">Admin</div> : ''}
									{ data.donation >= 1 ? <div className="label label-success">Donator</div> : ''}
									{ data.beta >= 1 ? <div className="label label-primary">Beta</div> : ''}
								</div>
							</div>
						</div>
					</div>
					<div className="profile-body">
						<div className="container">
							<div className="row">
								<div className="col-xs-12 col-lg-10 col-lg-offset-1">
									<div className="title">
										User Aliases
									</div>
								</div>
								<div className="col-xs-12 col-md-6 col-lg-5 col-lg-offset-1">
									<div className="table-responsive">
										<table className="table">
											<thead>
												<tr>
													<th colSpan="2">Alias History</th>
												</tr>
												<tr>
													<th className="table-timedisplay">Used On</th>
													<th>Username</th>
												</tr>
											</thead>
											<tbody>
												{ alias_history }
											</tbody>
										</table>
									</div>
								</div>
								<div className="col-xs-12 col-md-6 col-lg-5">
									<div className="table-responsive">
										<table className="table">
											<thead>
												<tr>
													<th colSpan="2">Recent Aliases</th>
												</tr>
												<tr>
													<th className="table-timedisplay">Used On</th>
													<th>Username</th>
												</tr>
											</thead>
											<tbody>
												{ alias_recent }
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<hr className="divider" />
						</div>
					</div>
					<div className="profile-vacstatus">
						<div className="container">
							<div className="row">
								<div className="col-xs-12 col-lg-10 col-lg-offset-1">
									<div className="title">
										VacStatus Account
									</div>
								</div>
							</div>
							<div className="row">
								<div className="col-xs-12 col-md-6 col-lg-5 col-lg-offset-1">
									<div className="table-responsive">
										<table className="table">
											<thead>
												<tr>
													<th className="text-center" colSpan="2">Public Lists Being Tracked In</th>
												</tr>
												<tr>
													<th className="table-timedisplay">Added On</th>
													<th>Title</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>Aug 14 2015</td>
													<td><a href="#">asdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasd</a></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div className="col-xs-12 col-md-6 col-lg-5">
									<div className="table-responsive">
										<table className="table">
											<thead>
												<tr>
													<th className="text-center" colSpan="2">{ data.display_name }'s Public Lists</th>
												</tr>
												<tr>
													<th className="table-timedisplay">Created At</th>
													<th>Title</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>Aug 14 2015</td>
													<td><a href="#">asdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasd</a></td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<hr className="divider" />
							<div className="row">
								<div className="col-xs-12 col-md-4 col-md-offset-2">
									<h3 className="title">Extra Info</h3>
									<div className="content text-center">
										<div className="row">
											<div className="col-xs-6">
												<strong># of VAC Bans</strong><br />
													{ data.vac_bans }
											</div>
											<div className="col-xs-6">
												<strong># of Game Bans</strong><br />
													{ data.game_bans }
											</div>
										</div>
									</div>
								</div>
								<div className="col-xs-12 col-md-4">
									<h3 className="title">VacStatus Info</h3>
									<div className="content text-center">
										<div className="row">
											<div className="col-xs-6">
												<strong>First Checked</strong><br />
													{ data.created_at }
											</div>
											<div className="col-xs-6">
												<strong>Times Added</strong><br />
													{ data.times_added && data.times_added.number ? data.times_added.number : 0 } <sub>{ data.times_added && data.times_added.time ? "(" + data.times_added.time + ")" : ''}</sub>
											</div>
										</div>
									</div>
								</div>
							</div>
							<hr className="divider" />
						</div>
					</div>
					<ListHandler />
				</div>
			);
		} else {
			return (
				<div></div>
			)
		}
	}
});