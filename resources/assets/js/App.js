import React from 'react';
import Router from 'react-router';

var DefaultRoute = Router.DefaultRoute;
var Link = Router.Link;
var Route = Router.Route;
var RouteHandler = Router.RouteHandler;

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