'use strict';

import React from 'react';
import autobind from 'autobind-decorator';
import BasicComp from '../BasicComp';

import CreateList from './CreateList';
import EditList from './EditList';
import AddUserToList from './AddUserToList';
import AddUsersFromSearch from './AddUsersFromSearch';

export default class ListHandler extends BasicComp {
	constructor(props) {
		super(props);

		this.state = { list_info: [], current_list: {} };

		$(document).on("click", ".open-addUserModal", function(e) {
			let profileId = $(this).data('id');
			console.log([e]);
			$("#addUserModal").find('#addUserProfileId').val(profileId);
		});
	}

	componentDidMount() {
		this.fetchLists();
	}

	componentWillReceiveProps(props) {
		if(props.currentList == undefined) return;

		this.setState($.extend({}, this.state, {current_list: props.currentList}));
	}

	fetchLists() {
		if(!this.authCheck) return;

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

	saveNewDataForParent(data) {
		this.newTitle = data.title;
		this.newPrivacy = data.privacy;
	}

	updateLists(list_info) {
		this.setState({ list_info: list_info });
	}

	render() {
		return (
			<div>
				<CreateList CreateListSend={ this.submitNewListToServer }/>
				<EditList UpdateListTitle={ this.updateListTitle } editData={ this.state.current_list }/>
				<AddUserToList myList={ this.state.list_info } AddUserSend={ this.submitNewUserToServer }/>
				<AddUsersFromSearch myList={ this.state.list_info } addSearchUsers={this.submitSearchUserToServer}/>
			</div>
		);
	}

	@autobind
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

				this.notify.success('List has been created!').run();

				if(this.props.UpdateMyList !== undefined) this.props.UpdateMyList(data);
				this.updateLists(data);
			},
			complete: () => {
				delete this.request.submitNewListToServer;
			}
		});
	}

	@autobind
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

	@autobind
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

	@autobind
	updateListTitle(data) {
		this.props.updatedCurrentList(data);
	}
}