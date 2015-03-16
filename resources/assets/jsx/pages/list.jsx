var grab = $('#list').data('grab');

var List = React.createClass({
	fetchList: function()
	{
		$.ajax({
			url: '/api/v1/list/'+grab,
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
		this.fetchList();
	},

	getInitialState: function()
	{
		return {
			data: null
		};
	},

	render: function()
	{
		var data, author, list, smallActionBar, listElement, specialColors;

		data = this.state.data;

		if(data !== null)
		{
			if(data.author)
			{
				author = <div><small>By: { data.author }</small></div>;
			}

			if(data.list !== null)
			{
				list = data.list.map(function(profile, index)
				{
					specialColors = "";
					if(data.beta) specialColors = "beta";
					if(data.donation >= 10.0) specialColors = "donator";
					if(data.site_admin) specialColors = "admin";

					return (
						<tr key={index}>
							<td className="user_avatar">
								<img src={profile.avatar_thumb} />
							</td>
							<td className="user_name">
								<a className={"" + specialColors} href={"/u/" + profile.steam_64_bit} target="_blank">{profile.display_name}</a>
							</td>
							<td className="user_vac_ban text-center">
								<span className={"text-" + (profile.vac > 0 ? "danger" : "success")}>
									{ profile.vac > 0 ? profile.vac_banned_on : <span className="fa fa-times"></span> }
								</span>
							</td>
							<td className="user_community_ban text-center hidden-sm">
								<span className={"fa fa-"+(data.community ? 'check' : 'times')+" text-" + (data.community ? 'danger' : 'success')}></span>
							</td>
							<td className="user_trade_ban text-center hidden-sm">
								<span className={"fa fa-"+(data.trade ? 'check' : 'times')+" text-" + (data.trade ? 'danger' : 'success')}></span>
							</td>	
							<td className="user_track_number text-center">
								{ profile.times_added.number }
							</td>
						</tr>
					);
				});
			}

			smallActionBar = (
				<div className="list-action-bar hidden-lg">
					<div className="container">
						<div className="row">
							<div className="col-xs-12">
								<a href="#" data-toggle="collapse" data-target="#list-actions"><span className="fa fa-bars"></span>&nbsp; Advanced Options</a>
								<div id="list-actions" className="list-actions collapse">
									<ListAction />
								</div>
							</div>
						</div>
					</div>
				</div>
			)

			listElement = (
				<div className="container">
					<div className="row">
						<div className="col-lg-3">
							<div className="list-actions visible-lg-block">
								<ListAction />
							</div>
						</div>
						<div className="col-xs-12 col-lg-9">
							<h2 className="list-title">
								{ data.title } { author }
							</h2>
							<div className="table-responsive">
								<table className="table list-table">
									<tr>
										<th width="48"></th>
										<th>User</th>
										<th className="text-center" width="120">VAC Ban</th>
										<th className="text-center hidden-sm" width="140">Community Ban</th>
										<th className="text-center hidden-sm" width="100">Trade Ban</th>
										<th className="text-center" width="100">Tracked By</th>
									</tr>
									{ list }
								</table>
							</div>
						</div>
					</div>
				</div>
			);
		}

		return (
			<div>{ smallActionBar } { listElement }</div>
		);
	}
});

var ListAction = React.createClass({
	render: function()
	{
		return (
			<div className="list-action-container">
				<hr className="divider" />
				<div className="row">
					<div className="col-xs-12">
					</div>
					<div className="col-xs-12">
						<form action="" className="option-content">
							<h4 className="title">Create New List</h4>
							<div className="form-group">
								<select className="form-control">
									<option value="1">Public</option>
									<option value="2">Friends Only</option>
									<option value="3">Private</option>
								</select>
							</div>
							<div className="form-group">
								<input type="text" className="form-control" placeholder="List Name" />
							</div>
							<div className="form-group">
								<button type="submit" className="btn form-control">Create List</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		);
	}
});

React.render(<List />, document.getElementById('list'));