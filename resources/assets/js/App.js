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

		this.updateMyList = this.updateMyList.bind(this);
		this.state = { my_list: [] }
	}

	updateMyList(myList) {
		console.log('myList', myList);
		this.setState($.extend({}, this.state, { my_list: myList }));
	}

	render() {
		console.log('render', this.state);
		return (
			<div>
				<div className="wrap">
					<Header />
					{ React.cloneElement(this.props.children, { updateMyList: this.state.my_list }) }

					<div className="pushFooter" />
					<ListHandler UpdateMyList={ this.updateMyList } />
				</div>

				<Footer />
			</div>
		);
	}
}