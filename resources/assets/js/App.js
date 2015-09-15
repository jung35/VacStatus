'use strict';

import React from 'react';
import { Router, Route, Link, IndexRoute } from 'react-router'

import createBrowserHistory from 'history/lib/createBrowserHistory'

var authCheck = $('meta[name="auth"]').attr('content');
var _token = $('meta[name="_token"]').attr('content');

$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': _token } });

class App extends React.Component {
	constructor(props) {
		super(props);

		this.state = { my_list: [] };

		this.updateMyList = this.updateMyList.bind(this);
		this.updateCurrentList = this.updateCurrentList.bind(this);
	}

	updateMyList(myList) {
		this.setState($.extend({}, this.state, { my_list: myList }));
	}

	updateCurrentList(listInfo) {
		this.setState($.extend({}, this.state, { listInfo: listInfo }));
	}

	render() {
		return (
			<div>
				<div className="wrap">
					<Header />
					{ React.cloneElement(this.props.children, { parentState: this.state, currentList={ this.markCurrentList } }) }

					<div className="pushFooter" />
					<ListHandler currentList={ this.state.currentList } UpdateMyList={ this.updateMyList } />
				</div>

				<Footer />
			</div>
		);
	}
}