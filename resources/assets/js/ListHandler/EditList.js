'use strict';

import React from 'react';
import BasicComp from '../BasicComp';
import autobind from 'autobind-decorator';

export default class EditList extends BasicComp {
	componentWillReceiveProps(props) {
		if(props.editData == undefined || props.editData.title == null) return;

		this.setState({editData: props.editData});
	}

	submitEditedListToServer(listInfo) {
		this.request.submitEditedListToServer = $.ajax({
			url: '/api/v1/list/'+listInfo.list_id,
			dataType: 'json',
			type: 'POST',
			data: {
				title: listInfo.title,
				privacy: listInfo.privacy
			},
			success: (data) => {
				if(data.error) {
					this.notify.danger(data.error).run();
					return;
				}

				this.notify.success('List has been saved!').run();
				this.sendNewDataToParent(listInfo);
				console.log('EditList', 'submitEditedListToServer', listInfo);
			},
			complete: () => {
				delete this.request.submitEditedListToServer;
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

	sendNewDataToParent(data) {
		this.props.UpdateListTitle({
			title: data.title,
			privacy: data.privacy
		});
	}

	render() {
		let editData = this.state.editData;

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
								<button type="button" onClick={ this.handleDelete } className="btn btn-danger pull-left">Delete</button>
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
	handleDelete(e) {
		this.submitDeletedListToServer(this.state.editData.id);

		$('#editListModal').modal('hide');
	}

	@autobind
	handleSubmit(e) {
		e.preventDefault();

		let title = this.refs.editListTitle.getDOMNode().value.trim();
		let privacy = this.refs.editListPrivacy.getDOMNode().value.trim();
		let list_id = this.state.editData.id;

		console.log('EditList', 'handleSubmit', title, privacy, this.state.editData);

		if (!title || !privacy || !list_id) {
			this.notify.danger('All fields need to be filled out!').run();
			return;
		}

		this.submitEditedListToServer({
			list_id: list_id,
			title: title,
			privacy: privacy
		});

		$('#editListModal').modal('hide');
	}
}