'use strict';

import React from 'react';
import { Router, Route, IndexRoute } from 'react-router';
import createBrowserHistory from 'history/lib/createBrowserHistory';

import App from './App';
import Pages from './Pages';

export default (
	<Router history={ createBrowserHistory() }>
		<Route path="/" component={ App }>
			<IndexRoute component={ Pages.Home } />
			<Route path="news" component={ Pages.News }>
				<Route path=":page" component={ Pages.News }/>
			</Route>
			<Route path="list" component={ Pages.ListPortal }/>
			<Route path="list/*" component={ Pages.List }/>
			<Route path="u/:steamId" component={ Pages.Profile }/>
			<Route path="search/:searchId" component={ Pages.Search }/>
			<Route path="donate" component={ Pages.Home }/>
			<Route path="privacy" component={ Pages.Privacy }/>
		</Route>
	</Router>
);