import React from 'react';
import { Router, Route, RouteHandler, DefaultRoute, Link } from 'react-router'

var authCheck = $('meta[name="auth"]').attr('content');
var _token = $('meta[name="_token"]').attr('content');

$.ajaxSetup({
	headers: { 'X-CSRF-TOKEN': _token }
});

class App extends React.Component {
	render() {
		return (
			<div>
				<div className="wrap">
					<Header />
					<RouteHandler />

					<div className="pushFooter" />
				</div>

				<Footer />
			</div>
		);
	}
}