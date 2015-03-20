var grab = $('#list').data('grab');
var auth_check = $('meta[name=auth]').attr("content");

var List = React.createClass({
	UpdateListTitle: function(newData)
	{
		var data = this.state.data;
		data.title = newData.newTitle;
		data.privacy = newData.newPrivacy;

		this.setState({data: data});
	},

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

	submitDeleteUserToServer: function(profile)
	{
		$.ajax({
			url: '/api/v1/list/delete',
			dataType: 'json',
			type: 'POST',
			data: {
				_token: _token,
				_method: 'DELETE',
				list_id: this.state.data.id,
				profile_id: profile.id
			},
			success: function(data) {
				if(data.error) {
					notif.add('danger', data.error).run();
				} else {
					notif.add('success', 'User has been removed from the list!').run();
					this.setState({data: data});
				}
			}.bind(this),
				error: function(xhr, status, err) {
				notif.add('danger', err).run();
			}.bind(this)
		});
	},

	render: function()
	{
		var data,
			author,
			list,
			smallActionBar,
			listElement,
			specialColors,
			showListAction,
			listExtraInfo;

		data = this.state.data;

		if(data !== null)
		{
			if(data.error)
			{
				return <h1 className="text-center">{data.error}</h1>
			}

			if(data.author)
			{
				author = <div><small>By: { data.author }</small></div>;
			}

			if(data.list !== null && data.list !== undefined)
			{
				list = data.list.map(function(profile, index)
				{
					var auth;

					if(auth_check) {
						if(data.my_list) {
							auth = (
								<a className="userListModify open-addUserModal" href="#" onClick={this.submitDeleteUserToServer.bind(this, profile)} data-id={ profile.id }>
									<span className="fa fa-minus faText-align text-danger"></span>
								</a>
							);
						} else {
							auth = (
								<a className="userListModify open-addUserModal" href="#addUserModal" data-toggle="modal" data-id={ profile.id }>
									<span className="fa fa-plus faText-align text-primary"></span>
								</a>
							);
						}
					}
					specialColors = "";
					if(profile.beta) specialColors = "beta-name";
					if(profile.donation >= 10.0) specialColors = "donator-name";
					if(profile.site_admin) specialColors = "admin-name";

					return (
						<tr key={ index }>
							<td className="user_avatar">
								{ auth }<img src={profile.avatar_thumb} />
							</td>
							<td className="user_name">
								<a className={specialColors} href={"/u/" + profile.steam_64_bit} target="_blank">{profile.display_name}</a>
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
				}.bind(this));
			}

			if(data.privacy)
			{
				var privacy, privacy_color;

				switch(data.privacy)
				{
					case "3":
					case 3:
						privacy = "Private";
						privacy_color = "danger";
						break;
					case "2":
					case 2:
						privacy = "Friends Only";
						privacy_color = "warning";
						break;
					case "1":
					case 1:
						privacy = "Public";
						privacy_color = "success";
						break;
				}

				listExtraInfo = (
					<div className="col-xs-12 col-md-6">
						<div className="list-extra-info text-right">
							<div className={"list-type text-" + privacy_color}>{ privacy } List</div>
							<div>Subscribed Users: { data.sub_count }</div>
						</div>
					</div>
				);
			}

			if(auth_check && data.author)
			{
				var eListAction = <ListAction myList={data.my_list} />;
				smallActionBar = (
					<div className="list-action-bar hidden-lg">
						<div className="container">
							<div className="row">
								<div className="col-xs-12">
									<a href="#" data-toggle="collapse" data-target="#list-actions"><span className="fa fa-bars"></span>&nbsp; Advanced Options</a>
									<div id="list-actions" className="list-actions collapse">
										{ eListAction }
									</div>
								</div>
							</div>
						</div>
					</div>
				)

				showListAction = (
					<div className="col-lg-3">
						<div className="list-actions visible-lg-block">
							{ eListAction }
						</div>
					</div>
				);
			}

			listElement = (
				<div className="container">
					<div className="row">
						<div className={"col-xs-12" + (showListAction ? " col-lg-9": "" )}>
							<div className="row">
								<div className="col-xs-12 col-md-6">
									<h2 className="list-title">
										{ data.title } { author }
									</h2>
								</div>
								{ listExtraInfo }
							</div>
							<div className="table-responsive">
								<table className="table list-table">
									<thead>
										<tr>
											<th width="80"></th>
											<th>User</th>
											<th className="text-center" width="120">VAC Ban</th>
											<th className="text-center hidden-sm" width="140">Community Ban</th>
											<th className="text-center hidden-sm" width="100">Trade Ban</th>
											<th className="text-center" width="100">Tracked By</th>
										</tr>
									</thead>
									<tbody>
										{ list }
									</tbody>
								</table>
							</div>
						</div>
						{ showListAction }
					</div>
				</div>
			);
		}

		return (
			<div>{ smallActionBar } { listElement } <ListHandler UpdateListTitle={this.UpdateListTitle} editData={this.state.data} /></div>
		);
	}
});

var ListAction = React.createClass({
	render: function()
	{
		var editList;

		if(this.props.myList) {
			editList = (
				<div className="col-xs-6 col-lg-12">
					<button className="btn btn-block" data-toggle="modal" data-target="#editListModal">Edit List</button>
				</div>
			);
		}

		return (
			<div className="list-action-container">
				<hr className="divider" />
				<div className="row">
					{ editList }
					<div className="col-xs-6 col-lg-12">
						<button className="btn btn-block">Subscribe to List</button>
					</div>
				</div>
			</div>
		);
	}
});

React.render(<List />, document.getElementById('list'));