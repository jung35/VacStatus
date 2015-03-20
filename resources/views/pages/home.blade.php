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
					@if(!Auth::check())
					<a href="{{ route('auth.login') }}" class="btn">Get Started via STEAM</a>
					@endif
					<div id="hero-carousel" class="carousel slide" data-ride="carousel">
						<div class="carousel-inner" role="listbox">
							<div class="item active">
								<img src="/img/screenshot/0.png">
							</div>
							<div class="item">
								<img src="/img/screenshot/1.png">
							</div>
							<div class="item">
								<img src="/img/screenshot/2.png">
							</div>
							<div class="item">
								<img src="/img/screenshot/3.png">
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
						@foreach($parsedNews as $singleArticle)
						<li>
							<strong><a href="{{ route('news') }}">[{{ $singleArticle['created_at'] }}] {{ $singleArticle['title'] }}</a></strong>
						</li>
						@endforeach
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
							<i class="fa fa-github-square"></i>
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

				<div class="col-xs-12 col-md-6 evenitem">
					<div class="feature-item">
						<div class="item-img hidden-xs hidden-sm">
							<i class="fa fa-steam-square"></i>
						</div>
						<div class="item-content">
							<div class="title">Steam Integrated</div>
							<div class="content">
								<p>
									VacStatus uses Steam OpenID to login and register.
									Registration is not required to use and browse
									through this website, but there are some features
									that will be hidden from guest users.
								</p>
							</div>
						</div>
						<div class="item-img hidden-md hidden-lg">
							<i class="fa fa-steam-square"></i>
						</div>
					</div>
				</div>

				<div class="col-xs-12 col-md-6">
					<div class="feature-item">
						<div class="item-img">
							<i class="fa fa-share-alt-square"></i>
						</div>
						<div class="item-content">
							<div class="title">Create & Share Lists</div>
							<div class="content">
								<p>
									Create lists to keep track of hackers you meet
									and share them with your friends. Also, feel
									free to create up to 5 lists.
								</p>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xs-12 col-md-6 evenitem">
					<div class="feature-item">
						<div class="item-img hidden-xs hidden-sm">
							<i class="fa fa-envelope-square"></i>
						</div>
						<div class="item-content">
							<div class="title">Receive Notifications</div>
							<div class="content">
								<p>
									Subscribe up to 5 lists for free that you want to
									keep track of and receive notifiactions via Email
									or Pushbullet.
								</p>
							</div>
						</div>
						<div class="item-img hidden-md hidden-lg">
							<i class="fa fa-envelope-square"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="listHandler"></div>
@stop

@section('js')
	<script src="/js/pages/home.js"></script>
@stop