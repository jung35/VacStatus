'use strict';

class ListHandler extends BasicComp {
	constructor(props) {
		super(props);

		this.state = { list_info: [] };

		this.submitNewListToServer = this.submitNewListToServer.bind(this);
		this.submitEditedListToServer = this.submitEditedListToServer.bind(this);
		this.submitDeletedListToServer = this.submitDeletedListToServer.bind(this);
		this.submitNewUserToServer = this.submitNewUserToServer.bind(this);
		this.submitSearchUserToServer = this.submitSearchUserToServer.bind(this);
	}

	componentDidMount() {
		this.fetchLists();
	}

	fetchLists() {
		if(!authCheck) return;

		this.request.fetchLists = $.ajax({
			url: '/api/v1/list/simple',
			dataType: 'json',
			success: (data) => {
				this.updateLists(data);
			},
			complete: () => {
				delete this.request.fetchLists;
			}
		});
	}

	submitNewListToServer(data) {
		this.request.submitNewListToServer = $.ajax({
			url: '/api/v1/list',
			dataType: 'json',
			type: 'POST',
			data: {
				title: data.title,
				privacy: data.privacy
			},
			success: (data) => {
				if(data.error) {
					this.notify.danger(data.error).run();
					return;
				}

				console.log('notify', this.notify);

				this.notify.success('List has been created!').run();

				console.log('typeof', typeof this.props.UpdateMyList);
				if(this.props.UpdateMyList !== undefined)
				{
					this.props.UpdateMyList(data);
				}

				this.updateLists(data);
			},
			complete: () => {
				delete this.request.submitNewListToServer;
			}
		});
	}

	submitEditedListToServer(data) {
		this.saveNewDataForParent(data);

		this.request.submitEditedListToServer = $.ajax({
			url: '/api/v1/list/'+data.list_id,
			dataType: 'json',
			type: 'POST',
			data: {
				title: data.title,
				privacy: data.privacy
			},
			success: (data) => {
				if(data.error) {
					this.notify.danger(data.error).run();
					return;
				}

				this.notify.success('List has been saved!').run();
				this.updateLists(data);
				this.sendNewDataToParent();
			},
			complete: () => {
				delete this.request.submitEditedListToServer;
			}
		});
	}

	submitNewUserToServer(data) {
		this.request.submitNewUserToServer = $.ajax({
			url: '/api/v1/list/add',
			dataType: 'json',
			type: 'POST',
			data: {
				list_id: data.list_id,
				description: data.description,
				profile_id: data.profile_id
			},
			success: (data) => {
				if(data.error) {
					this.notify.danger(data.error).run();
					return;
				}

				this.notify.success('User has been added to the list!').run();
			},
			complete: () => {
				delete this.request.submitNewUserToServer;
			}
		});
	}

	submitDeletedListToServer(list_id) {
		this.request.submitDeletedListToServer = $.ajax({
			url: '/api/v1/list/'+list_id,
			dataType: 'json',
			type: 'POST',
			data: { _method: 'DELETE' },
			success: (data) => {
				if(data.error) {
					this.notify.danger(data.error).run();
					return;
				}

				this.notify.success('List has been deleted!').run();
				window.location = "/list";
			},
			error: (xhr, status, err) => {
				this.notify.danger(err).run();
			}
		});
	}

	submitSearchUserToServer(data) {
		let searchUsers = $('#searchUsers').text();

		this.request.submitSearchUserToServer = $.ajax({
			url: '/api/v1/list/add/many',
			dataType: 'json',
			type: 'POST',
			data: {
				search: searchUsers,
				list_id: data.list_id,
				description: data.description
			},
			success: (data) => {
				if(data.error) {
					this.notify.danger(data.error).run();
				} else {
					this.notify.success('Users have been added to the list!').run();
				}
			},
			error: (xhr, status, err) => {
				this.notify.danger(err).run();
			}
		});
	}

	saveNewDataForParent(data) {
		this.props.newTitle = data.title;
		this.props.newPrivacy = data.privacy;
	}

	sendNewDataToParent() {
		this.props.UpdateListTitle({
			newTitle: this.props.newTitle,
			newPrivacy: this.props.newPrivacy
		});
	}

	updateLists(list_info) {
		this.setState({list_info});
	}

	render() {
		return (
			<div>
				<CreateList
					CreateListSend={ this.submitNewListToServer }
				/>
				<EditList
					editData={this.props.editData}
					EditListSend={this.submitEditedListToServer}
					DeleteListSend={this.submitDeletedListToServer}
				/>
				<AddUserToList
					myList={this.state.list_info}
					AddUserSend={this.submitNewUserToServer}
				/>
				<AddUsersFromSearch
					myList={this.state.list_info}
					addSearchUsers={this.submitSearchUserToServer}
				/>
			</div>
		);
	}
}

class CreateList extends BasicComp {

	constructor(props) {
		super(props);

		this.handleSubmit = this.handleSubmit.bind(this);
	}

	handleSubmit(e) {
		e.preventDefault();

		let title = this.refs.createListTitle.getDOMNode().value.trim();
		let privacy = this.refs.createListPrivacy.getDOMNode().value.trim();

		if (!title || !privacy) {
			this.notify.danger('All fields need to be filled out!').run();
			return;
		}

		this.props.CreateListSend({
			title: title,
			privacy: privacy
		});

		this.refs.createListTitle.getDOMNode().value = '';
		this.refs.createListPrivacy.getDOMNode().value = '1';

		$('#createListModal').modal('hide');
	}

	render() {
		return (
			<div className="modal fade" id="createListModal" tabIndex="-1" role="dialog">
				<div className="modal-dialog">
					<div className="modal-content">
						<div className="modal-header">
							<button type="button" className="close" data-dismiss="modal"><span>&times;</span></button>
							<h4 className="modal-title">Create New List</h4>
						</div>
						<form onSubmit={this.handleSubmit}>
							<div className="modal-body">
								<div className="form-group">
									<label htmlFor="createList-title">List Name</label>
									<input id="createList-title" ref="createListTitle" className="form-control" type="text" />
								</div>
								<div className="form-group">
									<label htmlFor="createList-privacy">List Permission</label>
									<select id="createList-privacy" ref="createListPrivacy" className="form-control">
										<option value="1">Public</option>
										<option value="2">Friends Only</option>
										<option value="3">Private</option>
									</select>
								</div>
							</div>
							<div className="modal-footer">
								<button type="button" className="btn btn-default" data-dismiss="modal">Close</button>
								<button type="submit" className="btn btn-primary">Save changes</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		);
	}
};

class EditList extends BasicComp {

	constructor(props) {
		super(props);

		this.handleSubmit = this.handleSubmit.bind(this);
		this.handleDelete = this.handleDelete.bind(this);
	}

	handleDelete(e) {
		this.props.DeleteListSend(this.props.editData.id);

		$('#editListModal').modal('hide');
	}

	handleSubmit(e) {
		e.preventDefault();

		let title = this.refs.editListTitle.getDOMNode().value.trim();
		let privacy = this.refs.editListPrivacy.getDOMNode().value.trim();
		let list_id = this.props.editData.id;

		if (!title || !privacy || !list_id) {
			this.notify.danger('All fields need to be filled out!').run();
			return;
		}

		this.props.EditListSend({
			list_id: list_id,
			title: title,
			privacy: privacy
		});

		$('#editListModal').modal('hide');
	}

	render() {
		let editData = this.props.editData;

		if(editData == null || editData.title == null) return <div></div>;

		return (
			<div className="modal fade" id="editListModal" tabIndex="-1" role="dialog">
				<div className="modal-dialog">
					<div className="modal-content">
						<div className="modal-header">
							<button type="button" className="close" data-dismiss="modal"><span>&times;</span></button>
							<h4 className="modal-title">Edit List</h4>
						</div>
						<form onSubmit={ this.handleSubmit }>
							<div className="modal-body">
								<div className="form-group">
									<label htmlFor="createList-title">List Name</label>
									<input id="createList-title" ref="editListTitle" className="form-control" type="text" defaultValue={ editData.title } />
								</div>
								<div className="form-group">
									<label htmlFor="createList-privacy">List Permission</label>
									<select id="createList-privacy" ref="editListPrivacy" className="form-control" defaultValue={ editData.privacy }>
										<option value="1">Public</option>
										<option value="2">Friends Only</option>
										<option value="3">Private</option>
									</select>
								</div>
							</div>
							<div className="modal-footer">
								<button type="button" onClick={ this.handleDelete} className="btn btn-danger pull-left">Delete</button>
								<button type="button" className="btn btn-default" data-dismiss="modal">Close</button>
								<button type="submit" className="btn btn-primary">Save changes</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		);
	}
};

$(document).on("click", ".open-addUserModal", function()
{
	let profileId = $(this).data('id');
	$("#addUserModal").find('#addUserProfileId').val(profileId);
});	

class AddUserToList extends BasicComp {

	constructor(props) {
		super(props);

		this.handleSubmit = this.handleSubmit.bind(this);
	}

	handleSubmit(e) {
		e.preventDefault();

		let list_id = this.refs.addUserList.getDOMNode().value.trim();
		let description = this.refs.addUserDescription.getDOMNode().value.trim();
		let profile_id = this.refs.addUserProfileId.getDOMNode().value.trim();

		if (!list_id) {
			this.notify.danger('Please select a list!').run();
			return;
		}

		if(!profile_id) {
			this.notify.danger('Please select a user!').run();
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
	}

	render() {
		let listOptions = this.props.myList.map((list, key) => {
			return <option key={ key } value={ list.id }>{ list.title }</option>;
		});

		return (
			<div className="modal fade" id="addUserModal" tabIndex="-1" role="dialog">
				<div className="modal-dialog">
					<div className="modal-content">
						<div className="modal-header">
							<button type="button" className="close" data-dismiss="modal"><span>&times;</span></button>
							<h4 className="modal-title">Add User to List</h4>
						</div>
						<form onSubmit={this.handleSubmit}>
							<div className="modal-body">
								<div className="form-group">
									<label htmlFor="addUser-list">Select a List</label>
									<select id="addUser-list" ref="addUserList" className="form-control">
									{ listOptions }
									</select>
								</div>
								<div className="form-group">
									<label htmlFor="addUser-description">User Description</label>
									<textarea id="addUser-description" ref="addUserDescription" className="form-control" placeholder="Few words to remind you who this person is."></textarea>
								</div>
							</div>
							<div className="modal-footer">
								<input id="addUserProfileId" type="hidden" ref="addUserProfileId" />
								<button type="button" className="btn btn-default" data-dismiss="modal">Close</button>
								<button type="submit" className="btn btn-primary">Add User</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		);
	}
};

class AddUsersFromSearch extends BasicComp {

	constructor(props) {
		super(props);

		this.handleSubmit = this.handleSubmit.bind(this);
	}

	handleSubmit(e) {
		e.preventDefault();

		let list_id = this.refs.addUserList.getDOMNode().value.trim();
		let description = this.refs.addUserDescription.getDOMNode().value.trim();

		if (!list_id) {
			this.notify.danger('Please select a list!').run();
			return;
		}

		this.props.addSearchUsers({
			list_id: list_id,
			description: description
		});

		this.refs.addUserDescription.getDOMNode().value = '';

		$('#addAllUsers').modal('hide');
	}

	render() {
		let listOptions = this.props.myList.map((list, key) => {
			return <option key={ key } value={ list.id }>{ list.title }</option>;
		});

		return (
			<div className="modal fade" id="addAllUsers" tabIndex="-1" role="dialog">
				<div className="modal-dialog">
					<div className="modal-content">
						<div className="modal-header">
							<button type="button" className="close" data-dismiss="modal"><span>&times;</span></button>
							<h4 className="modal-title">Add All Users to List</h4>
						</div>
						<form onSubmit={this.handleSubmit}>
							<div className="modal-body">
								<div className="form-group">
									<label htmlFor="addUser-list">Select a List</label>
									<select id="addUser-list" ref="addUserList" className="form-control">
									{ listOptions }
									</select>
								</div>
								<div className="form-group">
									<label htmlFor="addUser-description">Description</label>
									<textarea id="addUser-description" ref="addUserDescription" className="form-control" placeholder="Few words to remind you who this person is."></textarea>
								</div>
							</div>
							<div className="modal-footer">
								<button type="button" className="btn btn-default" data-dismiss="modal">Close</button>
								<button type="submit" className="btn btn-primary">Add Users</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		);
	}
}