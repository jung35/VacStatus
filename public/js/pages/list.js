var grab = $('#list').data('grab');

var List = React.createClass({displayName: "List",
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
				author = React.createElement("div", null, React.createElement("small", null, "By: ",  data.author));
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
						React.createElement("tr", {key: index}, 
							React.createElement("td", {className: "user_avatar"}, 
								React.createElement("img", {src: profile.avatar_thumb})
							), 
							React.createElement("td", {className: "user_name"}, 
								React.createElement("a", {className: "" + specialColors, href: "/u/" + profile.steam_64_bit, target: "_blank"}, profile.display_name)
							), 
							React.createElement("td", {className: "user_vac_ban text-center"}, 
								React.createElement("span", {className: "text-" + (profile.vac > 0 ? "danger" : "success")}, 
									 profile.vac > 0 ? profile.vac_banned_on : React.createElement("span", {className: "fa fa-times"})
								)
							), 
							React.createElement("td", {className: "user_community_ban text-center hidden-sm"}, 
								React.createElement("span", {className: "fa fa-"+(data.community ? 'check' : 'times')+" text-" + (data.community ? 'danger' : 'success')})
							), 
							React.createElement("td", {className: "user_trade_ban text-center hidden-sm"}, 
								React.createElement("span", {className: "fa fa-"+(data.trade ? 'check' : 'times')+" text-" + (data.trade ? 'danger' : 'success')})
							), 	
							React.createElement("td", {className: "user_track_number text-center"}, 
								 profile.times_added.number
							)
						)
					);
				});
			}

			smallActionBar = (
				React.createElement("div", {className: "list-action-bar hidden-lg"}, 
					React.createElement("div", {className: "container"}, 
						React.createElement("div", {className: "row"}, 
							React.createElement("div", {className: "col-xs-12"}, 
								React.createElement("a", {href: "#", "data-toggle": "collapse", "data-target": "#list-actions"}, React.createElement("span", {className: "fa fa-bars"}), "  Advanced Options"), 
								React.createElement("div", {id: "list-actions", className: "list-actions collapse"}, 
									React.createElement(ListAction, null)
								)
							)
						)
					)
				)
			)

			listElement = (
				React.createElement("div", {className: "container"}, 
					React.createElement("div", {className: "row"}, 
						React.createElement("div", {className: "col-lg-3"}, 
							React.createElement("div", {className: "list-actions visible-lg-block"}, 
								React.createElement(ListAction, null)
							)
						), 
						React.createElement("div", {className: "col-xs-12 col-lg-9"}, 
							React.createElement("h2", {className: "list-title"}, 
								 data.title, " ", author 
							), 
							React.createElement("div", {className: "table-responsive"}, 
								React.createElement("table", {className: "table list-table"}, 
									React.createElement("tr", null, 
										React.createElement("th", {width: "48"}), 
										React.createElement("th", null, "User"), 
										React.createElement("th", {className: "text-center", width: "120"}, "VAC Ban"), 
										React.createElement("th", {className: "text-center hidden-sm", width: "140"}, "Community Ban"), 
										React.createElement("th", {className: "text-center hidden-sm", width: "100"}, "Trade Ban"), 
										React.createElement("th", {className: "text-center", width: "100"}, "Tracked By")
									), 
									list 
								)
							)
						)
					)
				)
			);
		}

		return (
			React.createElement("div", null, smallActionBar, " ", listElement )
		);
	}
});

var ListAction = React.createClass({displayName: "ListAction",
	render: function()
	{
		return (
			React.createElement("div", {className: "list-action-container"}, 
				React.createElement("hr", {className: "divider"}), 
				React.createElement("div", {className: "row"}, 
					React.createElement("div", {className: "col-xs-12"}
					), 
					React.createElement("div", {className: "col-xs-12"}, 
						React.createElement("form", {action: "", className: "option-content"}, 
							React.createElement("h4", {className: "title"}, "Create New List"), 
							React.createElement("div", {className: "form-group"}, 
								React.createElement("select", {className: "form-control"}, 
									React.createElement("option", {value: "1"}, "Public"), 
									React.createElement("option", {value: "2"}, "Friends Only"), 
									React.createElement("option", {value: "3"}, "Private")
								)
							), 
							React.createElement("div", {className: "form-group"}, 
								React.createElement("input", {type: "text", className: "form-control", placeholder: "List Name"})
							), 
							React.createElement("div", {className: "form-group"}, 
								React.createElement("button", {type: "submit", className: "btn form-control"}, "Create List")
							)
						)
					)
				)
			)
		);
	}
});

React.render(React.createElement(List, null), document.getElementById('list'));