'use strict';

import React from 'react';
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
						<Subscription params={ this.props.params } />
						<UserKey />
					</div>
				</div>
			</div>
		)
	}
}