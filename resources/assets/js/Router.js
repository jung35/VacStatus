'use strict';

let routes = (
	<Route name="app" path="/" handler={ App }>
		<Route name="news" handler={ News }>
			<Route path=":page" handler={ News }/>
		</Route>
		<Route name="list" handler={ ListPortal }>
			<Route path="vac" handler={ ListPortal }/>
		</Route>
		<Route name="donate" handler={ Home }/>
    	<DefaultRoute handler={ Home } />
    </Route>
);

Router.run(routes, Router.HistoryLocation, (Handler) => {
	React.render(<Handler />, document.getElementById('app'));
});