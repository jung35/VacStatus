var _token = $('meta[name=_token]').attr("content");
var auth_check = $('meta[name=auth]').attr("content");

var ListHandler = React.createClass({displayName: "ListHandler",
	submitNewListToServer: function(data)
	{
		$.ajax({
			url: '/api/v1/list',
			dataType: 'json',
			type: 'POST',
			data: {
				_token: _token,
				title: data.title,
				privacy: data.privacy
			},
			success: function(data) {
				if(data.error) {
					notif.add('danger', data.error).run();
				} else {
					notif.add('success', 'List has been created!').run();
					if(this.props.UpdateMyList !== undefined)
					{
						this.props.UpdateMyList(data);
					}
					this.updateLists(data);
				}
			}.bind(this),
				error: function(xhr, status, err) {
				notif.add('danger', err).run();
			}.bind(this)
		});
	},

	submitEditedListToServer: function(data)
	{
		this.saveNewDataForParent(data);

		$.ajax({
			url: '/api/v1/list/'+data.list_id,
			dataType: 'json',
			type: 'POST',
			data: {
				_token: _token,
				title: data.title,
				privacy: data.privacy
			},
			success: function(data) {
				if(data.error) {
					notif.add('danger', data.error).run();
				} else {
					notif.add('success', 'List has been saved!').run();
					this.updateLists(data);
					this.sendNewDataToParent();
				}
			}.bind(this),
				error: function(xhr, status, err) {
				notif.add('danger', err).run();
			}.bind(this)
		});
	},

	submitNewUserToServer: function(data)
	{
		$.ajax({
			url: '/api/v1/list/add',
			dataType: 'json',
			type: 'POST',
			data: {
				_token: _token,
				list_id: data.list_id,
				description: data.description,
				profile_id: data.profile_id
			},
			success: function(data) {
				if(data.error) {
					notif.add('danger', data.error).run();
				} else {
					notif.add('success', 'User has been added to the list!').run();
				}
			}.bind(this),
				error: function(xhr, status, err) {
				notif.add('danger', err).run();
			}.bind(this)
		});
	},

	saveNewDataForParent: function(data)
	{
		this.props.newTitle = data.title;
		this.props.newPrivacy = data.privacy;
	},

	sendNewDataToParent: function()
	{
		this.props.UpdateListTitle({
			newTitle: this.props.newTitle,
			newPrivacy: this.props.newPrivacy
		});
	},

	submitDeletedListToServer: function(list_id)
	{
		$.ajax({
			url: '/api/v1/list/'+list_id,
			dataType: 'json',
			type: 'POST',
			data: {
				_token: _token,
				_method: 'DELETE'
			},
			success: function(data) {
				if(data.error) {
					notif.add('danger', data.error).run();
				} else {
					notif.add('success', 'List has been deleted!').run();
					window.location = "/list";
				}
			}.bind(this),
				error: function(xhr, status, err) {
				notif.add('danger', err).run();
			}.bind(this)
		});
	},

	fetchLists: function()
	{
		if(!auth_check) return;
		$.ajax({
			url: '/api/v1/list/simple',
			dataType: 'json',
			success: function(data) {
				this.updateLists(data);
			}.bind(this),
			error: function(xhr, status, err) {
				notif.add('danger', err).run();
			}.bind(this)
		});
	},

	updateLists: function(lists)
	{
		this.setState({lists: lists});
	},

	componentDidMount: function()
	{
		this.fetchLists();
	},

	getInitialState: function()
	{
		return {
			lists: []
		};
	},

	render: function()
	{
		return (
			React.createElement("div", null, 
				React.createElement(CreateList, {
					CreateListSend: this.submitNewListToServer}
				), 
				React.createElement(EditList, {
					editData: this.props.editData, 
					EditListSend: this.submitEditedListToServer, 
					DeleteListSend: this.submitDeletedListToServer}
				), 
				React.createElement(AddUserToList, {
					myList: this.state.lists, 
					AddUserSend: this.submitNewUserToServer}
				)
			)
		);
	}
});

var CreateList = React.createClass({displayName: "CreateList",

	handleSubmit: function(e) {
		e.preventDefault();

		var title = this.refs.createListTitle.getDOMNode().value.trim(),
			privacy = this.refs.createListPrivacy.getDOMNode().value.trim();

		if (!title || !privacy) {
			notif.add('danger', 'All fields need to be filled out!').run();
			return;
		}

		this.props.CreateListSend({
			title: title,
			privacy: privacy
		});

		this.refs.createListTitle.getDOMNode().value = '';
		this.refs.createListPrivacy.getDOMNode().value = '1';

		$('#createListModal').modal('hide');
	},

	render: function()
	{
		return (
			React.createElement("div", {className: "modal fade", id: "createListModal", tabIndex: "-1", role: "dialog"}, 
				React.createElement("div", {className: "modal-dialog"}, 
					React.createElement("div", {className: "modal-content"}, 
						React.createElement("div", {className: "modal-header"}, 
							React.createElement("button", {type: "button", className: "close", "data-dismiss": "modal"}, React.createElement("span", null, "×")), 
							React.createElement("h4", {className: "modal-title"}, "Create New List")
						), 
						React.createElement("form", {onSubmit: this.handleSubmit}, 
							React.createElement("div", {className: "modal-body"}, 
								React.createElement("div", {className: "form-group"}, 
									React.createElement("label", {htmlFor: "createList-title"}, "List Name"), 
									React.createElement("input", {id: "createList-title", ref: "createListTitle", className: "form-control", type: "text"})
								), 
								React.createElement("div", {className: "form-group"}, 
									React.createElement("label", {htmlFor: "createList-privacy"}, "List Permission"), 
									React.createElement("select", {id: "createList-privacy", ref: "createListPrivacy", className: "form-control"}, 
										React.createElement("option", {value: "1"}, "Public"), 
										React.createElement("option", {value: "2"}, "Friends Only"), 
										React.createElement("option", {value: "3"}, "Private")
									)
								)
							), 
							React.createElement("div", {className: "modal-footer"}, 
								React.createElement("button", {type: "button", className: "btn btn-default", "data-dismiss": "modal"}, "Close"), 
								React.createElement("button", {type: "submit", className: "btn btn-primary"}, "Save changes")
							)
						)
					)
				)
			)
		);
	}
});

var EditList = React.createClass({displayName: "EditList",

	handleDelete: function(e) {
		var list_id = this.props.editData.id;
		this.props.DeleteListSend(list_id);
		$('#editListModal').modal('hide');
	},

	handleSubmit: function(e) {
		e.preventDefault();

		var title = this.refs.editListTitle.getDOMNode().value.trim(),
			privacy = this.refs.editListPrivacy.getDOMNode().value.trim(),
			list_id = this.props.editData.id;

		if (!title || !privacy || !list_id) {
			notif.add('danger', 'All fields need to be filled out!').run();
			return;
		}

		this.props.EditListSend({
			list_id: list_id,
			title: title,
			privacy: privacy
		});

		$('#editListModal').modal('hide');
	},

	render: function()
	{
		var editData = this.props.editData;

		if(editData !== null && editData !== undefined)
		{
			return (
				React.createElement("div", {className: "modal fade", id: "editListModal", tabIndex: "-1", role: "dialog"}, 
					React.createElement("div", {className: "modal-dialog"}, 
						React.createElement("div", {className: "modal-content"}, 
							React.createElement("div", {className: "modal-header"}, 
								React.createElement("button", {type: "button", className: "close", "data-dismiss": "modal"}, React.createElement("span", null, "×")), 
								React.createElement("h4", {className: "modal-title"}, "Edit List")
							), 
							React.createElement("form", {onSubmit: this.handleSubmit}, 
								React.createElement("div", {className: "modal-body"}, 
									React.createElement("div", {className: "form-group"}, 
										React.createElement("label", {htmlFor: "createList-title"}, "List Name"), 
										React.createElement("input", {id: "createList-title", ref: "editListTitle", className: "form-control", type: "text", defaultValue:  editData.title})
									), 
									React.createElement("div", {className: "form-group"}, 
										React.createElement("label", {htmlFor: "createList-privacy"}, "List Permission"), 
										React.createElement("select", {id: "createList-privacy", ref: "editListPrivacy", className: "form-control", defaultValue:  editData.privacy}, 
											React.createElement("option", {value: "1"}, "Public"), 
											React.createElement("option", {value: "2"}, "Friends Only"), 
											React.createElement("option", {value: "3"}, "Private")
										)
									)
								), 
								React.createElement("div", {className: "modal-footer"}, 
									React.createElement("button", {type: "button", onClick: this.handleDelete, className: "btn btn-danger pull-left"}, "Delete"), 
									React.createElement("button", {type: "button", className: "btn btn-default", "data-dismiss": "modal"}, "Close"), 
									React.createElement("button", {type: "submit", className: "btn btn-primary"}, "Save changes")
								)
							)
						)
					)
				)
			);
		}

		return React.createElement("div", null);

	}
});

$(document).on("click", ".open-addUserModal", function()
{
	var profileId = $(this).data('id');
	$("#addUserModal").find('#addUserProfileId').val(profileId);
});	

var AddUserToList = React.createClass({displayName: "AddUserToList",
	handleSubmit: function(e)
	{
		e.preventDefault();

		var list_id = this.refs.addUserList.getDOMNode().value.trim(),
			description = this.refs.addUserDescription.getDOMNode().value.trim(),
			profile_id = this.refs.addUserProfileId.getDOMNode().value.trim();

		if (!list_id) {
			notif.add('danger', 'Please select a list!').run();
			return;
		}

		if(!profile_id) {
			notif.add('danger', 'Please select a user!').run();
			return;
		}

		this.props.AddUserSend({
			list_id: list_id,
			description: description,
			profile_id: profile_id
		});

		this.refs.addUserDescription.getDOMNode().value = '';
		this.refs.addUserProfileId.getDOMNode().value = '';

		$('#addUserModal').modal('hide');
	},

	render: function()
	{
		var listOptions = this.props.myList.map(function(list, key) {
			return React.createElement("option", {key: key, value:  list.id},  list.title);
		});

		return (
			React.createElement("div", {className: "modal fade", id: "addUserModal", tabIndex: "-1", role: "dialog"}, 
				React.createElement("div", {className: "modal-dialog"}, 
					React.createElement("div", {className: "modal-content"}, 
						React.createElement("div", {className: "modal-header"}, 
							React.createElement("button", {type: "button", className: "close", "data-dismiss": "modal"}, React.createElement("span", null, "×")), 
							React.createElement("h4", {className: "modal-title"}, "Add User to List")
						), 
						React.createElement("form", {onSubmit: this.handleSubmit}, 
							React.createElement("div", {className: "modal-body"}, 
								React.createElement("div", {className: "form-group"}, 
									React.createElement("label", {htmlFor: "addUser-list"}, "Select a List"), 
									React.createElement("select", {id: "addUser-list", ref: "addUserList", className: "form-control"}, 
									listOptions 
									)
								), 
								React.createElement("div", {className: "form-group"}, 
									React.createElement("label", {htmlFor: "addUser-description"}, "User Description"), 
									React.createElement("textarea", {id: "addUser-description", ref: "addUserDescription", className: "form-control", placeholder: "Few words to remind you who this person is."})
								)
							), 
							React.createElement("div", {className: "modal-footer"}, 
								React.createElement("input", {id: "addUserProfileId", type: "hidden", ref: "addUserProfileId"}), 
								React.createElement("button", {type: "button", className: "btn btn-default", "data-dismiss": "modal"}, "Close"), 
								React.createElement("button", {type: "submit", className: "btn btn-primary"}, "Add User")
							)
						)
					)
				)
			)
		);
	}
});