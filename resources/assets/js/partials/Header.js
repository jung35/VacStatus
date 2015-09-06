'use strict';

class Header extends React.Component {
	navHeader () {
		return (
			<div className="navbar-header">
				<button type="button" className="navbar-toggle collapsed" data-toggle="collapse" data-target="#vacstatus-navbar">
					<span className="sr-only">Toggle navigation</span>
					<span className="icon-bar"></span>
					<span className="icon-bar"></span>
					<span className="icon-bar"></span>
				</button>
				<div className="navbar-display-logo">
					<a className="navbar-brand" href="/">VacStatus</a>
				</div>
			</div>
        )
	}

	navLeft () {
		let createList;

		if(authCheck) createList = <li><a href="#createListModal" data-toggle="modal">Create List</a></li>;

		return (
			<ul className="nav navbar-nav">
				<li><Link to="app">Home</Link></li>
				<li><Link to="news">News</Link></li>
				<li><Link to="list">Lists</Link></li>
				{ createList }
				<li><a href="#searchModal" data-toggle="modal">Look Up Users</a></li>
			</ul>
        );
	}

	navRight () {
		let navProfile = <li><a className="steam-small-login" href="/auth/login">Sign in through STEAM</a></li>;

		if(authCheck)
		{
			navProfile = (
				<li className="dropdown">
					<a href="#" className="dropdown-toggle" data-toggle="dropdown">
						<div className="nav-username">asd</div>
						<div className="nav-avatar"><img src="" /></div>
					</a>
					<ul className="dropdown-menu" role="menu">
						<li><a href="/u/">Profile</a></li>
						<li><a href="/settings">Settings</a></li>
						<li className="divider" />
						<li><a href="/auth/logout">Sign Out</a></li>
					</ul>
				</li>
			);
		}

		return (
			<ul className="nav navbar-nav navbar-right">
				{ navProfile }
				<li>
					<Link className="heart-red" to="donate">
						<span className="fa fa-heart"></span>
					</Link>
				</li>
			</ul>
		);
	}

	render() {
		return (
			<nav className="navbar navbar-default">
				<div className="container">
					{ this.navHeader() }

					<div className="collapse navbar-collapse" id="vacstatus-navbar">
						{ this.navLeft() }{ this.navRight() }
					</div>
				</div>
			</nav>
		)
	}
}