'use strict';

class Home extends React.Component {
	render() {
		return (
	        <div className="home-page">
				<div className="hero">
					<div className="container">
						<div className="row">
							<div className="col-xs-12">
								<div className="logo">
									VacStatus
									<div className="sub-text">
										Keep track of people's VAC status in a list
									</div>

								</div>

								{ !authCheck ? <a href="/auth/login" className="btn">Get Started via STEAM</a> : null}

								<div id="hero-carousel" className="carousel slide" data-ride="carousel">
									<div className="carousel-inner" role="listbox">
										<div className="item active">
											<img src="/img/screenshot/0.png" />
										</div>
										<div className="item">
											<img src="/img/screenshot/1.png" />
										</div>
										<div className="item">
											<img src="/img/screenshot/2.png" />
										</div>
										<div className="item">
											<img src="/img/screenshot/3.png" />
										</div>
									</div>

									<a className="left carousel-control" href="#hero-carousel" role="button" data-slide="prev">
										<span className="fa fa-chevron-left"></span>
									</a>
									<a className="right carousel-control" href="#hero-carousel" role="button" data-slide="next">
										<span className="fa fa-chevron-right"></span>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div className="flash flash-announcement">
					<div className="container">
						<div className="row">
							<div className="col-xs-12">
								<div className="title">Announcement</div>
								<div className="content"></div>
							</div>
						</div>
					</div>
				</div>

				<div className="flash flash-news">
					<div className="container">
						<div className="row">
							<div className="col-xs-12">
								<div className="title">News</div>
								<ul className="content">
									<li><strong><Link to="news">[202020] asdasd</Link></strong></li>
									<li><strong><Link to="news">[123123] asdasd</Link></strong></li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<div className="feature-list">
					<div className="container">
						<div className="row">
							<div className="col-xs-12 col-md-6">
								<div className="feature-item">
									<div className="item-img">
										<i className="fa fa-github-square"></i>
									</div>
									<div className="item-content">
										<div className="title">Open Source</div>
										<div className="content">
											<p>
												All of the code behind it can be seen on Github.
												Feel free to take a look at it and maybe fix bugs
												and add features you would like to see added.
											</p>
										</div>
									</div>
								</div>
							</div>

							<div className="col-xs-12 col-md-6 evenitem">
								<div className="feature-item">
									<div className="item-img hidden-xs hidden-sm">
										<i className="fa fa-steam-square"></i>
									</div>
									<div className="item-content">
										<div className="title">Steam Integrated</div>
										<div className="content">
											<p>
												VacStatus uses Steam OpenID to login and register.
												Registration is not required to use and browse
												through this website, but there are some features
												that will be hidden from guest users.
											</p>
										</div>
									</div>
									<div className="item-img hidden-md hidden-lg">
										<i className="fa fa-steam-square"></i>
									</div>
								</div>
							</div>

							<div className="col-xs-12 col-md-6">
								<div className="feature-item">
									<div className="item-img">
										<i className="fa fa-share-alt-square"></i>
									</div>
									<div className="item-content">
										<div className="title">Create & Share Lists</div>
										<div className="content">
											<p>
												Create lists to keep track of hackers you meet
												and share them with your friends. Also, feel
												free to create up to 5 lists.
											</p>
										</div>
									</div>
								</div>
							</div>

							<div className="col-xs-12 col-md-6 evenitem">
								<div className="feature-item">
									<div className="item-img hidden-xs hidden-sm">
										<i className="fa fa-envelope-square"></i>
									</div>
									<div className="item-content">
										<div className="title">Receive Notifications</div>
										<div className="content">
											<p>
												Subscribe up to 5 lists for free that you want to
												keep track of and receive notifiactions via Email
												or Pushbullet.
											</p>
										</div>
									</div>
									<div className="item-img hidden-md hidden-lg">
										<i className="fa fa-envelope-square"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
        	</div>
        );
	}
}