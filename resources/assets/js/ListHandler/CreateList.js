'use strict';

import React from 'react';
import BasicComp from '../BasicComp';
import autobind from 'autobind-decorator';

export default class CreateList extends BasicComp {
	render() {
		return (
			<div className="modal fade" id="createListModal" tabIndex="-1" role="dialog">
				<div className="modal-dialog">
					<div className="modal-content">
						<div className="modal-header">
							<button type="button" className="close" data-dismiss="modal"><span>&times;</span></button>
							<h4 className="modal-title">Create New List</h4>
						</div>
						<form onSubmit={ this.handleSubmit }>
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

	@autobind
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
}