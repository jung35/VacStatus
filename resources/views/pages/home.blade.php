@extends('layout.app')

@section('content')
<div class="home-page">
	<div class="hero">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="logo">
						VacStat.us
						<div class="sub-text">
							Keep track of people's VAC status in a list
						</div>
					</div>

					<a href="#" class="btn">Get Started via STEAM</a>

					<div id="hero-carousel" class="carousel slide" data-ride="carousel">
						<div class="carousel-inner" role="listbox">
							<div class="item active">
								<img src="/img/screenshot/1.png">
							</div>
							<div class="item">
								<img src="/img/screenshot/1.png">
							</div>
							<div class="item">
								<img src="/img/screenshot/1.png">
							</div>
						</div>

						<a class="left carousel-control" href="#hero-carousel" role="button" data-slide="prev">
							<span class="fa fa-chevron-left"></span>
						</a>
						<a class="right carousel-control" href="#hero-carousel" role="button" data-slide="next">
							<span class="fa fa-chevron-right"></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="flash flash-announcement">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="title">Announcement</div>
					<div class="content">
						<strong><a href="#">Email & Subscription Implemented</a> -</strong> You can now recieve notifications!
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="flash flash-news">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="title">News</div>
					<ul class="content">
						<li>
							<strong><a href="#">[Jan 01 2015] Error in Email Updates</a></strong>
						</li>
						<li>
							<strong><a href="#">[Jan 02 2015] Mass add on search & pushbullet notification</a></strong>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="feature-list">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-md-6">
					<div class="feature-item">
						<div class="item-img">
							<i class="fa fa-github"></i>
						</div>
						<div class="item-content">
							<div class="title">Open Source</div>
							<div class="content">
								<p>
									All of the code behind it can be seen on Github.
									Feel free to take a look at it and maybe fix bugs
									and add features you woud like to see added.
								</p>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xs-12 col-md-6 text-right">
					<div class="feature-item">
						<div class="item-content">
							<div class="title">Open Source</div>
							<div class="content">
								<p>
									All of the code behind it can be seen on Github.
									Feel free to take a look at it and maybe fix bugs
									and add features you woud like to see added.
								</p>
							</div>
						</div>
						<div class="item-img">
							<i class="fa fa-github"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop