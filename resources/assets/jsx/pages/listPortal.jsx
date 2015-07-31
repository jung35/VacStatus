var ListPortal = React.createClass({
	UpdateMyList: function(myNewList)
	{
		this.setState($.extend({}, this.state, {my_list: myNewList}));
	},

	fetchLists: function()
	{
		$.ajax({
			url: '/api/v1/list',
			dataType: 'json',
			success: function(data) {
				this.setState($.extend({}, this.state, data));
			}.bind(this),
			error: function(xhr, status, err) {
				console.error(this.props.url, status, err.toString());
			}.bind(this)
		});
	},

	componentDidMount: function()
	{
		this.fetchLists();
	},

	getInitialState: function()
	{
		return { my_list: [], friends_list: [] };
	},

	listPrivacy: function(privacy)
	{
		var type = {};
		switch(privacy)
		{
			case "3":
			case 3:
				type.name = "Private";
				type.color = "danger";
				break;
			case "2":
			case 2:
				type.name = "Friends Only";
				type.color = "warning";
				break;
			default:
				type.name = "Public";
				type.color = "success";
				break;
		}

		return type;
	},

	userTitle: function(data)
	{
		var title;
		if(data.beta >= 1) title = "beta-name";
		if(data.donation >= 10.0) title = "donator-name";
		if(data.site_admin >= 1) title = "admin-name";

		return title;
	},

	renderMyList: function(data)
	{
		if(data.length < 1) return <div className="custom-list"></div>;

		var myList = data.map(function(list, index)
		{
			privacy = this.listPrivacy(list.privacy);

			return (
				<tr key={index}>
					<td className="text-center">{ list.id }</td>
					<td><a className="list_link" href={"/list/" + list.id}>{ list.title }</a></td>
					<td className={"text-center text-" + privacy.color}>{ privacy.name }</td>
					<td className="text-center">{ list.users_in_list }</td>
					<td className="text-center">{ list.sub_count }</td>
					<td className="text-center">{ list.created_at }</td>
				</tr>
			);
		}.bind(this));

		return (
			<div className="custom-list">
				<h3>My Lists</h3>
				<div className="table-responsive">
					<table className="table">
						<thead>
							<tr>
								<th className="text-center" width="25px">ID</th>
								<th>List Name</th>
								<th className="text-center" width="120px">List Type</th>
								<th className="text-center" width="120px">Users In List</th>
								<th className="text-center" width="120px">Subscribers</th>
								<th className="text-center" width="120px">List Creation</th>
							</tr>
						</thead>
						<tbody>
							{ myList }
						</tbody>
					</table>
				</div>
			</div>
		);
	},

	renderFriendsList: function(data)
	{
		if(data.length < 1) return <div className="custom-list"></div>;

		var friendsList = data.map(function(list, index)
		{
			var privacy = this.listPrivacy(list.privacy);
			var userTitle = this.userTitle(list);

			return (
				<tr key={index}>
					<td className="text-center">
						<img src={ list.avatar_thumb } />
					</td>
					<td><a className="list_link" href={"/list/" + list.user_list_id}>{ list.title }</a></td>
					<td className={ userTitle }>{ list.display_name }</td>
					<td className={"text-center text-" + privacy.color}>{ privacy.name }</td>
					<td className="text-center">{ list.users_in_list }</td>
					<td className="text-center">{ list.sub_count }</td>
					<td className="text-center">{ list.created_at }</td>
				</tr>
			);
		}.bind(this));

		return (
			<div className="custom-list">
				<h3>Friends&#39; Lists</h3>
				<div className="table-responsive">
					<table className="table">
						<thead>
							<tr>
								<th className="text-center" width="32px"></th>
								<th>List Name</th>
								<th width="200px">User</th>
								<th className="text-center" width="120px">List Type</th>
								<th className="text-center" width="120px">Users In List</th>
								<th className="text-center" width="120px">Subscribers</th>
								<th className="text-center" width="120px">List Creation</th>
							</tr>
						</thead>
						<tbody>
							{ friendsList }
						</tbody>
					</table>
				</div>
			</div>
		);
	},
	
	render: function()
	{
		var myList, friendsList;

		myList = this.renderMyList(this.state.my_list);
		friendsList = this.renderFriendsList(this.state.friends_list);

		return (
			<div className="container">
				<div className="row">
					<div className="col-xs-12">
						<div className="special-list">
							<a href="/list/most">Most Tracked Users</a>
							<a href="/list/latest">Latest Added Users</a>
							<a href="/list/latest/vac">Latest VAC Bans</a>
						</div>
					</div>
				</div>
				<div className="row">
					<div className="col-xs-12">{ myList }</div>
				</div>
				<div className="row">
					<div className="col-xs-12">{ friendsList }</div>
				</div>
				<ListHandler UpdateMyList={this.UpdateMyList} />
			</div>
     	);
	}
});

React.render(<ListPortal />, document.getElementById('listPortal'));