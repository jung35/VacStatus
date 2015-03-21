var ListPortal = React.createClass({displayName: "ListPortal",
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
				return React.createElement("h1", {className: "text-center"}, data.error)
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
						React.createElement("tr", {key: index}, 
							React.createElement("td", {className: "text-center"},  list.id), 
							React.createElement("td", null, React.createElement("a", {className: "list_link", href: "/list/" + list.id},  list.title)), 
							React.createElement("td", {className: "text-center text-" + privacy_color}, privacy ), 
							React.createElement("td", {className: "text-center"},  list.users_in_list), 
							React.createElement("td", {className: "text-center"},  list.sub_count), 
							React.createElement("td", {className: "text-center"},  list.created_at)
						)
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

					specialColors = "";
					if(list.beta) specialColors = "beta-name";
					if(list.donation >= 10.0) specialColors = "donator-name";
					if(list.site_admin >= 1) specialColors = "admin-name";

					return (
						React.createElement("tr", {key: index}, 
							React.createElement("td", {className: "text-center"}, 
								React.createElement("img", {src:  list.avatar_thumb})
							), 
							React.createElement("td", null, React.createElement("a", {className: "list_link", href: "/list/" + list.user_list_id},  list.title)), 
							React.createElement("td", {className: specialColors },  list.display_name), 
							React.createElement("td", {className: "text-center text-" + privacy_color}, privacy ), 
							React.createElement("td", {className: "text-center"},  list.users_in_list), 
							React.createElement("td", {className: "text-center"},  list.sub_count), 
							React.createElement("td", {className: "text-center"},  list.created_at)
						)
					);
				})
			}
		}
		return (
		React.createElement("div", {className: "container"}, 
			React.createElement("div", {className: "row"}, 
				React.createElement("div", {className: "col-xs-12"}, 
					React.createElement("div", {className: "special-list"}, 
						React.createElement("h3", null, "Special Lists"), 
						React.createElement("a", {href: "/list/most"}, "Most Tracked Users"), 
						React.createElement("a", {href: "/list/latest"}, "Latest Added Users")
					)
				), 
				React.createElement("div", {className: "col-xs-12"}, 
					React.createElement("div", {className: "custom-list"}, 
						React.createElement("h3", null, "My Lists"), 
						React.createElement("div", {className: "table-responsive"}, 
							React.createElement("table", {className: "table"}, 
								React.createElement("thead", null, 
									React.createElement("tr", null, 
										React.createElement("th", {className: "text-center", width: "25px"}, "ID"), 
										React.createElement("th", null, "List Name"), 
										React.createElement("th", {className: "text-center", width: "120px"}, "List Type"), 
										React.createElement("th", {className: "text-center", width: "120px"}, "Users In List"), 
										React.createElement("th", {className: "text-center", width: "120px"}, "Subscribers"), 
										React.createElement("th", {className: "text-center", width: "120px"}, "List Creation")
									)
								), 
								React.createElement("tbody", null, 
									my_list 
								)
							)
						)
					)
				), 
				React.createElement("div", {className: "col-xs-12"}, 
					React.createElement("div", {className: "custom-list"}, 
						React.createElement("h3", null, "Friends' Lists"), 
						React.createElement("div", {className: "table-responsive"}, 
							React.createElement("table", {className: "table"}, 
								React.createElement("thead", null, 
									React.createElement("tr", null, 
										React.createElement("th", {className: "text-center", width: "32px"}), 
										React.createElement("th", null, "List Name"), 
										React.createElement("th", {width: "200px"}, "User"), 
										React.createElement("th", {className: "text-center", width: "120px"}, "List Type"), 
										React.createElement("th", {className: "text-center", width: "120px"}, "Users In List"), 
										React.createElement("th", {className: "text-center", width: "120px"}, "Subscribers"), 
										React.createElement("th", {className: "text-center", width: "120px"}, "List Creation")
									)
								), 
								React.createElement("tbody", null, 
									friends_list 
								)
							)
						)
					)
				)
			), 
			React.createElement(ListHandler, {UpdateMyList: this.UpdateMyList})
		)
     	);
	}
});

React.render(React.createElement(ListPortal, null), document.getElementById('listPortal'));