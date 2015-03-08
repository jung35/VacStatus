@extends('layout.app')

@section('content')
<div class="home">
	<div class="hero">
		<div class="logo">
			VacStat.us
			<div class="sub-text">
				Keep track of people's VAC status in a list
			</div>
		</div>

		<a href="#" class="btn steam-big">Get Started via STEAM</a>

		<div id="hero-carousel" class="carousel slide" data-ride="carousel">
			<!-- Indicators -->
			<ol class="carousel-indicators">
				<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
				<li data-target="#carousel-example-generic" data-slide-to="1"></li>
				<li data-target="#carousel-example-generic" data-slide-to="2"></li>
			</ol>

			<!-- Wrapper for slides -->
			<div class="carousel-inner" role="listbox">
				<div class="item active">
					<img src="..." alt="...">
					<div class="carousel-caption">
					...
					</div>
				</div>
				<div class="item">
					<img src="..." alt="...">
					<div class="carousel-caption">
					...
					</div>
				</div>
				...
			</div>

			<!-- Controls -->
			<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>
	</div>

	<div class="flash flash-announcement">
		<div class="title">Announcement</div>
		<div class="content">
			<strong>Email & Subscription Implemented -</strong> You can now recieve notifications!
		</div>
	</div>

	<div class="flash flash-news">
		<div class="title">News</div>
		<ul class="content">
			<li>
				<strong>[Jan 01 2015] Error in Email Updates</strong>
			</li>
			<li>
				<strong>[Jan 02 2015] Mass add on search & pushbullet notification</strong>
			</li>
		</ul>
	</div>

	<div class="feature-list">
		<div class="feature-item feature-github">
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
@stop