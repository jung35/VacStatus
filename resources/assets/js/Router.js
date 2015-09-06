'use strict';

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
	constructor () {
		super();

		this.state = {};
	}

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

let routes = (
	<Route name="app" path="/" handler={ App }>
		<Route name="news" handler={ Home }/>
		<Route name="list" handler={ Home }/>
		<Route name="donate" handler={ Home }/>
    	<DefaultRoute handler={ Home } />
    </Route>
);

Router.run(routes, Router.HistoryLocation, function (Handler) {
	React.render(<Handler />, document.getElementById('app'));
});