'use strict';

import React from 'react';
import { Link } from 'react-router';
import autobind from 'autobind-decorator';
import BasicComp from '../../BasicComp';

export default class Subscription extends BasicComp {
	componentDidMount() {
		this.fetchSubscription();
		let params = this.props.params;
		if(params == undefined) return;

		let email = params.email;
		let code = params.code;

		if(email === undefined || code === undefined) return;

		this.validateEmail(email, code);
	}

	fetchSubscription() {
		this.request.fetchSubscription = $.ajax({
			url: '/api/v1/settings/subscribe',
			dataType: 'json',
			success: (data) => {
				this.setState(data);
			},
			complete: () => {
				delete this.request.fetchSubscription;
			}
		});
	}

	validateEmail(email, code) {
		this.request.validateEmail = $.ajax({
			url: '/api/v1/settings/subscribe/' + email + '/' + code,
			dataType: 'text',
			success: (data) => {
				if(data == 'success')
				{
					this.notify.success('Email has been verified!').run();
					this.fetchSubscription();
				}
				else if(data == 'error') this.notify.error('Email could not be verified or it is already verified.').run();

				history.pushState({}, 'VacStatus', '/settings');
			},
			complete: () => {
				delete this.request.validateEmail;
			}
		});
	}
	
	render() {
		let state = this.state;
		let userLists = state.userLists;
		let userMail = state.userMail;

		let emailColor, pushBulletColor, email, pushBullet, emailInput, pushBulletInput;

		if(userMail !== undefined)
		{
			if(userMail.email)
			{
				email = userMail.email;
				emailColor = 'has-warning';
				if(userMail.verify == "verified") emailColor = "has-success";
			}

			if(userMail.pushbullet)
			{
				pushBullet = userMail.pushbullet;
				pushBulletColor = 'has-warning';
				if(userMail.pushbullet_verify == "verified") pushBulletColor = "has-success";
			}

			emailInput = (
				<div className={ "form-group " + emailColor }>
					<label htmlFor="subcribeEmail" className="col-sm-2 control-label">Email</label>
					<div className={ emailColor ? "col-sm-8 " : "col-sm-10" }>
						<input type="email" className="form-control" id="subcribeEmail" ref="subcribeEmail" placeholder="Email" defaultValue={ email } />
					</div>
					{ emailColor ? (
						<div className="col-sm-2">
							<button type="button" onClick={ this.emailRemove } className="btn btn-block btn-danger">Remove</button>
						</div>
					) : "" }
				</div>
			);

			pushBulletInput = (
				<div className={ "form-group " + pushBulletColor }>
					<label htmlFor="subcribePushBullet" className="col-sm-2 control-label">Pushbullet</label>
					<div className={ pushBulletColor ? "col-sm-8" : "col-sm-10"}>
						<input type="email" className="form-control" id="subcribePushBullet" ref="subcribePushBullet" placeholder="PushBullet Email" defaultValue={ pushBullet } />
					</div>
					{ pushBulletColor ? (
						<div className="col-sm-2">
							<button type="button" onClick={ this.pushBulletRemove } className="btn btn-block btn-danger">Remove</button>
						</div>
					) : "" }
				</div>
			);
		}

		let subscribedLists = <div className="col-xs-12"><i>You're not subscribed to any list.</i></div>;

		if(userLists !== undefined)
		{
			subscribedLists = userLists.map((list, key) => {
				let specialColors = this.userTitle(list);

				return (
					<div key={ key } className="col-xs-6 col-sm-4">
						<Link to={"/list/" + list.id}>
							<div className="panel panel-default">
								<div className="panel-body">
									<div className="list-name">{ list.title }</div>
									<div className={"list-author " + specialColors}>{ list.display_name }</div>
								</div>
							</div>
						</Link>
					</div>
				);
			});
		}

		return (
			<div id="subscription" className="subscription-settings">
				<div className="row">
					<div className="col-xs-12 col-md-6">
						<h3>Receive Updates <small>&mdash; You only need to enter in one of them</small></h3>
						<form onSubmit={this.handleSubmit} className="settings-form form-horizontal">
							{ emailInput }
							{ pushBulletInput }
							<div className="form-group">
								<div className="col-sm-offset-2 col-sm-10">
									<button className="btn btn-block btn-primary">Save Settings</button>
								</div>
							</div>
						</form>
					</div>
					<div className="col-xs-12 col-md-6">
						<h3>Subscribed Lists <small>&mdash; You need to subscribe a list to receive notification</small></h3>
						<div className="subscribed-list">
							<div className="row">{ subscribedLists }</div>
						</div>
					</div>
				</div>
			</div>
		);
	}

	@autobind
	handleSubmit(e) {
		e.preventDefault();

		let email = this.refs.subcribeEmail.getDOMNode().value.trim();
		let push_bullet = this.refs.subcribePushBullet.getDOMNode().value.trim();

		if (!email && !push_bullet) {
			this.notify.danger('Atleast 1 field needs to be filled out!').run();
			return;
		}

		this.request.handleSubmit = $.ajax({
			url: '/api/v1/settings/subscribe',
			type: 'POST',
			data: {
				email: email,
				push_bullet: push_bullet
			},
			dataType: 'json',
			success: (data) => {
				if(data.error)
				{
					this.notify.danger(data.error).run();
					return;
				}

				this.notify
					.success('Settings have been saved!')
					.warning('Please check your email to verify!')
					.run();
				this.setState(data);
			},
			complete: () => {
				delete this.request.handleSubmit;
			}
		});
	}

	@autobind
	emailRemove(e) {
		e.preventDefault();

		this.request.emailRemove = $.ajax({
			url: '/api/v1/settings/subscribe/email',
			type: 'POST',
			data: { _method: 'DELETE' },
			dataType: 'json',
			success: (data) => {
				if(data.error)
				{
					this.notify.danger(data.error).run();
					return;
				}

				$('#subcribeEmail').val("");
				this.notify.success('Successfully removed email!').run();
				this.setState(data);
			},
			complete: () => {
				delete this.request.emailRemove;
			}
		});
	}

	@autobind
	pushBulletRemove(e) {
		e.preventDefault();

		this.request.pushBulletRemove = $.ajax({
			url: '/api/v1/settings/subscribe/pushbullet',
			type: 'POST',
			data: { _method: 'DELETE' },
			dataType: 'json',
			success: (data) => {
				if(data.error)
				{
					this.notify.add('danger', data.error).run();
					return;
				}

				$('#subcribePushBullet').val("");
				this.notify.success('Successfully removed pushbullet!').run();
				this.setState(data);
			},
			complete: () => {
				delete this.request.pushBulletRemove;
			}
		});
	}
}