var grab = $('#list').data('grab'),
	searchKey = $('#list').data('search');

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
		var url = '/api/v1/list/'+grab;
		if(grab == 'search')
		{
			url = 'api/v1/search/'+searchKey
		}

		$.ajax({
			url: url,
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

	componentDidUpdate: function()
	{
		$('[data-toggle="tooltip"]').tooltip()
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

	submitSubscriptionToServer: function()
	{
		$.ajax({
			url: '/api/v1/list/subscribe/' + this.state.data.id,
			dataType: 'json',
			type: 'POST',
			data: { _token: _token, },
			success: function(data) {
				if(data.error) {
					notif.add('danger', data.error).run();
				} else {
					notif.add('success', 'You have subscribed to the list!').run();
					this.setState({data: data});
				}
			}.bind(this),
				error: function(xhr, status, err) {
				notif.add('danger', err).run();
			}.bind(this)
		});

	},

	submitUnsubscriptionToServer: function()
	{
		$.ajax({
			url: '/api/v1/list/subscribe/' + this.state.data.id,
			dataType: 'json',
			type: 'POST',
			data: {
				_token: _token,
				_method: 'DELETE',
			},
			success: function(data) {
				if(data.error) {
					notif.add('danger', data.error).run();
				} else {
					notif.add('success', 'You have unsubscribed from the list!').run();
					this.setState({data: data});
				}
			}.bind(this),
				error: function(xhr, status, err) {
				notif.add('danger', err).run();
			}.bind(this)
		});
	},

	submitManyUsersToServer: function(data)
	{
		$.ajax({
			url: '/api/v1/list/add/many',
			dataType: 'json',
			type: 'POST',
			data: {
				_token: _token,
				search: data.search,
				description: data.description,
				list_id: grab
			},
			success: function(data) {
				this.setState({data: data});
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
			showListAction,
			listExtraInfo,
			steam_64_bit_list = [];

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
					var auth, specialColors, profile_description;

					steam_64_bit_list.push(profile.steam_64_bit);

					if(auth_check) {
						if(data.my_list) {
							auth = (
								React.createElement("span", {className: "pointer userListModify open-addUserModal", onClick: this.submitDeleteUserToServer.bind(this, profile), "data-id":  profile.id}, 
									React.createElement("span", {className: "fa fa-minus faText-align text-danger"})
								)
							);
						} else {
							auth = (
								React.createElement("a", {className: "userListModify open-addUserModal", href: "#addUserModal", "data-toggle": "modal", "data-id":  profile.id}, 
									React.createElement("span", {className: "fa fa-plus faText-align text-primary"})
								)
							);
						}
					}

					specialColors = "";
					if(profile.beta >= 1) specialColors = "beta-name";
					if(profile.donation >= 10.0) specialColors = "donator-name";
					if(profile.site_admin >= 1) specialColors = "admin-name";

					if(profile.profile_description)
					{
						profile_description = React.createElement("i", {className: "fa fa-eye pointer", "data-toggle": "tooltip", "data-placement": "right", title:  profile.profile_description})
					}

					return (
						React.createElement("tr", {key: index }, 
							React.createElement("td", {className: "user_avatar"}, 
								auth, React.createElement("img", {src: profile.avatar_thumb})
							), 
							React.createElement("td", {className: "user_name"}, 
								profile_description, " ", React.createElement("a", {className: specialColors, href: "/u/" + profile.steam_64_bit, target: "_blank"}, profile.display_name)
							), 
							React.createElement("td", {className: "user_vac_ban text-center"}, 
								React.createElement("span", {className: "text-" + (profile.vac > 0 ? "danger" : "success")}, 
									 profile.vac > 0 ? profile.vac_banned_on : React.createElement("span", {className: "fa fa-times"})
								)
							), 
							React.createElement("td", {className: "user_community_ban text-center hidden-sm"}, 
								React.createElement("span", {className: "fa fa-"+(profile.community >= 1 ? 'check' : 'times')+" text-" + (profile.community >= 1 ? 'danger' : 'success')})
							), 
							React.createElement("td", {className: "user_trade_ban text-center hidden-sm"}, 
								React.createElement("span", {className: "fa fa-"+(profile.trade >= 1 ? 'check' : 'times')+" text-" + (profile.trade >= 1  ? 'danger' : 'success')})
							), 	
							React.createElement("td", {className: "user_track_number text-center"}, 
								 profile.times_added.number
							)
						)
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
					React.createElement("div", {className: "col-xs-12 col-md-6"}, 
						React.createElement("div", {className: "list-extra-info text-right"}, 
							React.createElement("div", {className: "list-type text-" + privacy_color}, privacy, " List"), 
							React.createElement("div", null, "Subscribed Users: ",  data.sub_count)
						)
					)
				);
			}

			if(grab == "search")
			{
				var eListAction = (
					React.createElement("div", {className: "list-action-container"}, 
						React.createElement("hr", {className: "divider"}), 
						React.createElement("div", {className: "row"}, 
							React.createElement("div", {className: "col-xs-6 col-lg-12"}, 
								React.createElement("button", {className: "btn btn-block btn-info", "data-toggle": "modal", "data-target": "#addAllUsers"}, "Add All Users to List")
							)
						), 
						React.createElement("div", {id: "searchUsers", className: "hidden"},  steam_64_bit_list.join(" ") )
					)
				);
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

			if(auth_check && data.author)
			{
				var eListAction = React.createElement(ListAction, {addMany: this.submitManyUsersToServer, ListSubscribe: this.submitSubscriptionToServer, ListUnsubscribe: this.submitUnsubscriptionToServer, data: data});
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
									React.createElement("thead", null, 
										React.createElement("tr", null, 
											React.createElement("th", {width: "80"}), 
											React.createElement("th", null, "User"), 
											React.createElement("th", {className: "text-center", width: "140"}, "VAC / Game Ban"), 
											React.createElement("th", {className: "text-center hidden-sm", width: "140"}, "Community Ban"), 
											React.createElement("th", {className: "text-center hidden-sm", width: "100"}, "Trade Ban"), 
											React.createElement("th", {className: "text-center", width: "100"}, "Tracked By")
										)
									), 
									React.createElement("tbody", null, 
										list 
									)
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
	doSub: function()
	{
		this.props.ListSubscribe();
	},

	doUnsub: function()
	{
		this.props.ListUnsubscribe();
	},

	addMany: function(e)
	{
		e.preventDefault();

		var search = this.refs.search.getDOMNode().value.trim();
		var description = this.refs.description.getDOMNode().value.trim();

		this.props.addMany({search: search, description: description});

		this.refs.search.getDOMNode().value = '';
		this.refs.description.getDOMNode().value = '';
	},

	render: function()
	{
		var data, editList, subButton, addUsers;

		data = this.props.data;

		if(data !== null)
		{
			if(data.my_list) {
				editList = (
					React.createElement("div", {className: "col-xs-6 col-lg-12"}, 
						React.createElement("button", {className: "btn btn-block btn-info", "data-toggle": "modal", "data-target": "#editListModal"}, "Edit List")
					)
				);

				addUsers = (
					React.createElement("div", {className: "col-xs-6 col-lg-12"}, React.createElement("br", null), 
						React.createElement("form", {onSubmit: this.addMany}, 
							React.createElement("div", {className: "form-group"}, 
								React.createElement("label", {className: "label-control"}, 
									React.createElement("strong", null, "Add Users to List")
								), 
								React.createElement("textarea", {ref: "search", className: "form-control", rows: "10", 
placeholder: "2 ways to search: =================================" + ' ' +
 "- type in steam URL/id/profile and split them in spaces or newlines or both =================================" + ' ' +
 "- Type 'status' on console and paste the output here"})
							), 
							React.createElement("div", {className: "form-group"}, 
								React.createElement("textarea", {ref: "description", className: "form-control", rows: "3", placeholder: "A little description to help remember"})
							), 
							React.createElement("button", {className: "btn btn-block btn-primary form-control"}, "Add Users")
						)
					)
				);
			}

			subButton = (
				React.createElement("div", {className: "col-xs-6 col-lg-12"}, 
					React.createElement("button", {className: "btn btn-block", disabled: "disabled"}, "Subscribe to List"), 
					React.createElement("div", {className: "text-center"}, 
						React.createElement("small", null, React.createElement("i", null, "Please go to settings and verify email"))
					)
				)
			);

			if(data.can_sub)
			{
				subButton = (
					React.createElement("div", {className: "col-xs-6 col-lg-12"}, 
						React.createElement("button", {onClick:  this.doSub, className: "btn btn-block btn-primary"}, "Subscribe to List")
					)
				);

				if(data.subscription !== null) 
				{
					subButton = (
						React.createElement("div", {className: "col-xs-6 col-lg-12"}, 
							React.createElement("button", {onClick:  this.doUnsub, className: "btn btn-block btn-danger"}, "Unubscribe to List")
						)
					);
				}
			}

			return (
				React.createElement("div", {className: "list-action-container"}, 
					React.createElement("hr", {className: "divider"}), 
					React.createElement("div", {className: "row"}, 
						editList, 
						subButton, 
						addUsers 
					)
				)
			);
		}

		return React.createElement("div", null)
	}
});

React.render(React.createElement(List, null), document.getElementById('list'));