var grab = $('#list').data('grab');
var auth_check = $('meta[name=auth]').attr("content");

var List = React.createClass({displayName: "List",
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
				return React.createElement("h1", {className: "text-center"}, data.error)
			}

			if(data.author)
			{
				author = React.createElement("div", null, React.createElement("small", null, "By: ",  data.author));
			}

			if(data.list !== null && data.list !== undefined)
			{
				list = data.list.map(function(profile, index)
				{
					specialColors = "";
					if(profile.beta) specialColors = "beta";
					if(profile.donation >= 10.0) specialColors = "donator";
					if(profile.site_admin) specialColors = "admin";

					return (
						React.createElement("tr", {key: index}, 
							React.createElement("td", {className: "user_avatar"}, 
								React.createElement("img", {src: profile.avatar_thumb})
							), 
							React.createElement("td", {className: "user_name"}, 
								React.createElement("a", {className: specialColors + "-name", href: "/u/" + profile.steam_64_bit, target: "_blank"}, profile.display_name)
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
					React.createElement("div", {className: "col-xs-12 col-md-6"}, 
						React.createElement("div", {className: "list-extra-info text-right"}, 
							React.createElement("div", {className: "list-type text-" + privacy_color}, privacy, " List"), 
							React.createElement("div", null, "Subscribed Users: ",  data.sub_count)
						)
					)
             	);
			}

			if(auth_check && data.author)
			{
				var eListAction = React.createElement(ListAction, {myList: data.my_list});
				smallActionBar = (
					React.createElement("div", {className: "list-action-bar hidden-lg"}, 
						React.createElement("div", {className: "container"}, 
							React.createElement("div", {className: "row"}, 
								React.createElement("div", {className: "col-xs-12"}, 
									React.createElement("a", {href: "#", "data-toggle": "collapse", "data-target": "#list-actions"}, React.createElement("span", {className: "fa fa-bars"}), "  Advanced Options"), 
									React.createElement("div", {id: "list-actions", className: "list-actions collapse"}, 
										eListAction 
									)
								)
							)
						)
					)
				)

				showListAction = (
	              	React.createElement("div", {className: "col-lg-3"}, 
						React.createElement("div", {className: "list-actions visible-lg-block"}, 
							eListAction 
						)
					)
				);
			}

			listElement = (
				React.createElement("div", {className: "container"}, 
					React.createElement("div", {className: "row"}, 
						React.createElement("div", {className: "col-xs-12" + (showListAction ? " col-lg-9": "")}, 
							React.createElement("div", {className: "row"}, 
								React.createElement("div", {className: "col-xs-12 col-md-6"}, 
									React.createElement("h2", {className: "list-title"}, 
										 data.title, " ", author 
									)
								), 
								listExtraInfo 
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
						), 
						showListAction 
					)
				)
			);
		}

		return (
			React.createElement("div", null, smallActionBar, " ", listElement, " ", React.createElement(ListHandler, {UpdateListTitle: this.UpdateListTitle, editData: this.state.data}))
		);
	}
});

var ListAction = React.createClass({displayName: "ListAction",
	render: function()
	{
		var editList;

		if(this.props.myList) {
			editList = (
				React.createElement("div", {className: "col-xs-6 col-lg-12"}, 
					React.createElement("button", {className: "btn btn-block", "data-toggle": "modal", "data-target": "#editListModal"}, "Edit List")
				)
            );
		}

		return (
			React.createElement("div", {className: "list-action-container"}, 
				React.createElement("hr", {className: "divider"}), 
				React.createElement("div", {className: "row"}, 
					editList, 
					React.createElement("div", {className: "col-xs-6 col-lg-12"}, 
						React.createElement("button", {className: "btn btn-block"}, "Subscribe to List")
					)
				)
			)
		);
	}
});

React.render(React.createElement(List, null), document.getElementById('list'));