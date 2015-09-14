'use strict';

import React from 'react';
import { Router, Route, Link, IndexRoute } from 'react-router'

import createBrowserHistory from 'history/lib/createBrowserHistory'

var authCheck = $('meta[name="auth"]').attr('content');
var _token = $('meta[name="_token"]').attr('content');

$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': _token } });

class App extends React.Component {
	render() {
		return (
			<div>
				<div className="wrap">
					<Header />
					{ this.props.children }

					<div className="pushFooter" />
				</div>

				<Footer />
			</div>
		);
	}
}