'use strict';

import React from 'react';
import BasicComp from '../BasicComp';
import autobind from 'autobind-decorator';

export default class AddUserToList extends BasicComp {
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
						<form onSubmit={ this.handleSubmit }>
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

	@autobind
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
}