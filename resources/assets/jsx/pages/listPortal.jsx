var ListPortal = React.createClass({
	UpdateMyList: function(myNewList)
	{
		var data = this.state.data;
		data.my_list = myNewList;

		this.setState({ data: data });
	},

	fetchLists: function()
	{
		$.ajax({
			url: '/api/v1/list',
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
		this.fetchLists();
	},

	getInitialState: function()
	{
		return {
			data: null
		};
	},
	
	render: function()
	{
		var data, my_list, friends_list;

		data = this.state.data;

		if(data !== null)
		{
			if(data.error)
			{
				return <h1 className="text-center">{data.error}</h1>
			}

			if(data.my_list !== null && data.my_list !== undefined)
			{
				my_list = data.my_list.map(function(list, index)
				{
					var privacy, privacy_color;
					switch(list.privacy)
					{
						case 3:
							privacy = "Private";
							privacy_color = "danger";
							break;
						case 2:
							privacy = "Friends Only";
							privacy_color = "warning";
							break;
						default:
							privacy = "Public";
							privacy_color = "success";
							break;
					}

					return (
						<tr key={index}>
							<td className="text-center">{ list.id }</td>
							<td><a className="list_link" href={"/list/" + list.id}>{ list.title }</a></td>
							<td className={"text-center text-" + privacy_color}>{ privacy }</td>
							<td className="text-center">{ list.users_in_list }</td>
							<td className="text-center">{ list.sub_count }</td>
							<td className="text-center">{ list.created_at }</td>
						</tr>
					);
				})
			}

			if(data.friends_list !== null && data.friends_list !== undefined)
			{
				friends_list = data.friends_list.map(function(list, index)
				{
					var privacy, privacy_color, specialColors;
					switch(list.privacy)
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
						default:
							privacy = "Public";
							privacy_color = "success";
							break;
					}

					specialColors = "";
					if(list.beta >= 1) specialColors = "beta-name";
					if(list.donation >= 10.0) specialColors = "donator-name";
					if(list.site_admin >= 1) specialColors = "admin-name";

					return (
						<tr key={index}>
							<td className="text-center">
								<img src={ list.avatar_thumb } />
							</td>
							<td><a className="list_link" href={"/list/" + list.user_list_id}>{ list.title }</a></td>
							<td className={ specialColors }>{ list.display_name }</td>
							<td className={"text-center text-" + privacy_color}>{ privacy }</td>
							<td className="text-center">{ list.users_in_list }</td>
							<td className="text-center">{ list.sub_count }</td>
							<td className="text-center">{ list.created_at }</td>
						</tr>
					);
				})
			}
		}
		return (
		<div className="container">
			<div className="row">
				<div className="col-xs-12">
					<div className="special-list">
						<h3>Special Lists</h3>
						<a href="/list/most">Most Tracked Users</a>
						<a href="/list/latest">Latest Added Users</a>
					</div>
				</div>
				<div className="col-xs-12">
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
									{ my_list }
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div className="col-xs-12">
					<div className="custom-list">
						<h3>Friends' Lists</h3>
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
									{ friends_list }
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<ListHandler UpdateMyList={this.UpdateMyList} />
		</div>
     	);
	}
});

React.render(<ListPortal />, document.getElementById('listPortal'));