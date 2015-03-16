<nav class="navbar navbar-default">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#vacstatus-navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div class="{{ Route::currentRouteName() == 'home' ? 'visible-xs-inline': 'navbar-display-logo' }}">
				<a class="navbar-brand" href="{{ route('home') }}">VacStatus</a>
			</div>
		</div>

		<div class="collapse navbar-collapse" id="vacstatus-navbar">
			<ul class="nav navbar-nav">
				<li class="{{ Route::currentRouteName() != 'home' ? 'visible-xs-inline': '' }} @setActiveLink('home')">
					<a href="{{ route('home') }}">Home</a>
				</li>
				<li class="@setActiveLink('tracked.most')">
					<a href="{{ route('tracked.most') }}">Most Tracked</a>
				</li>
				<li class="@setActiveLink('tracked.latest')">
					<a href="{{ route('tracked.latest') }}">Latest Added</a>
				</li>
				<li class="@setActiveLink('search')">
					<a href="#">Search</a>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
			@if(\Auth::check())
				<li><a href="{{ route('auth.logout') }}">Sign out</a></li>
			@else
				<li><a class="steam-small-login" href="{{ route('auth.login') }}">Sign in through STEAM</a></li>
			@endif
				<li><a class="heart-red" href="#"><span class="fa fa-heart"></span></a></li>
			</ul>
		</div>
	</div>
</nav>