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
				<a class="navbar-brand" href="/">VacStatus</a>
			</div>
		</div>

		<div class="collapse navbar-collapse" id="vacstatus-navbar">
			<ul class="nav navbar-nav">
				<li>
					<a href="/home">Home</a>
				</li>
				<li>
					<a href="/news">News</a>
				</li>
				<li>
					<a href="/list">Lists</a>
				</li>

			@if(\Auth::check())
				<li>
					<a href="#createListModal" data-toggle="modal">Create List</a>
				</li>
			@endif

				<li>
					<a href="#searchModal" data-toggle="modal">Look Up Users</a>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
			@if(\Auth::check())
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<div class="nav-username">{{ Auth::user()->display_name }}</div>
						<div class="nav-avatar"><img src="{{ isset(Auth::user()->profile->avatar_thumb)? Auth::user()->profile->avatar_thumb:'' }}"></div>
					</a>
					<ul class="dropdown-menu" role="menu">
						<li>
							<a href="/u/{{ VacStatus\Steam\Steam::to64bit(Auth::user()->small_id) }}">Profile</a>
						</li>
						<li><a href="/settings">Settings</a></li>
						<li class="divider"></li>
						<li><a href="/auth/logout">Sign Out</a></li>
					</ul>
				</li>
			@else
				<li><a class="steam-small-login" href="/auth/login">Sign in through STEAM</a></li>
			@endif
				<li><a class="heart-red" href="/donate"><span class="fa fa-heart"></span></a></li>
			</ul>
		</div>
	</div>
</nav>