'use strict';

import React from 'react';
import autobind from 'autobind-decorator';
import BasicComp from '../../BasicComp';
import Subscription from './Subscription';
import UserKey from './UserKey';

export default class Settings extends BasicComp {
	render() {
		return  (
			<div className="container settings">
				<div className="row">
					<div className="col-xs-12">
						<h1>Settings</h1>
						{ this.authCheck ? (
							<div>
								<Subscription params={ this.props.params } />
								<UserKey />
							</div>
						) : this.redirectMe() }
					</div>
				</div>
			</div>
		)
	}

	@autobind
	redirectMe() {
		window.location.replace("/auth/login");
		return "Redirecting...";
	}
}