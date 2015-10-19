'use strict';

import React from 'react';
import autobind from 'autobind-decorator';
import BasicComp from '../../BasicComp';

export default class UserKey extends BasicComp {
	componentDidMount() {
		this.fetchUserKey();
	}

	fetchUserKey() {
		this.request.fetchUserKey = $.ajax({
			url: '/api/v1/settings/userkey',
			dataType: 'json',
			success: (data) => {
				this.setState(data);
			},
			complete: () => {
				delete this.request.fetchUserKey;
			}
		});
	}
	
	render() {
		let state = this.state;
		let userKeyValue = state !== undefined ? state.key : "";

		let userKeyForm = (
			<form onSubmit={ this.handleSubmit } className="settings-form form-horizontal">
				<div className="form-group">
					<label htmlFor="userKeyInput" className="col-sm-2 control-label">Key</label>
					<div className="col-sm-8">
						<input readOnly type="text" className="form-control" id="userKeyInput" ref="userKeyInput" placeholder="Press to Generate Key" value={ userKeyValue } />
					</div>
					<div className="col-sm-2">
						<button className="btn btn-block btn-primary">Generate</button>
					</div>
				</div>
			</form>
		);

		return (
			<div id="userKey" className="user-key-settings">
				<div className="row">
					<div className="col-xs-12 col-md-6">
						<h3>Private Key <small>&mdash; Use this key to give permission to 3rd party applications</small></h3>
						{ userKeyForm }
					</div>
				</div>
			</div>
		);
	}

	@autobind
	handleSubmit(e) {
		e.preventDefault();
		
		this.request.handleSubmit = $.ajax({
			url: '/api/v1/settings/userkey',
			type: 'POST',
			dataType: 'json',
			success: (data) => {
				this.setState(data);
			},
			complete: () => {
				delete this.request.handleSubmit;
			}
		});
	}
}