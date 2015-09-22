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

		this.state = {};

		this.updateMyList = this.updateMyList.bind(this);
		this.markCurrentList = this.markCurrentList.bind(this);
		this.updateCurrentList = this.updateCurrentList.bind(this);
	}

	updateMyList(myList) {
		this.setState($.extend({}, this.state, { my_list: myList }));
	}

	markCurrentList(listInfo) {
		this.setState($.extend({}, this.state, { listInfo: listInfo }));
		console.log('App', 'markCurrentList', this.state);
	}

	updateCurrentList(currentList) {
		this.state.listInfo = $.extend({}, this.state.listInfo, currentList)
		this.setState(this.state);
		console.log('App', 'updateCurrentList', this.state);
	}

	render() {
		return (
			<div>
				<div className="wrap">
					<Header />
					{ React.cloneElement(this.props.children, { parentState: this.state, updateCurrentList: this.markCurrentList }) }

					<div className="pushFooter" />
					<ListHandler currentList={ this.state.listInfo } updatedCurrentList={ this.updateCurrentList } UpdateMyList={ this.updateMyList } />
				</div>

				<Footer />
			</div>
		);
	}
}