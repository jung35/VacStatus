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
			<div class="visible-xs-inline">
				<a class="navbar-brand" href="#">VacStatus</a>
			</div>
		</div>

		<div class="collapse navbar-collapse" id="vacstatus-navbar">
			<ul class="nav navbar-nav">
				<li @setActiveLink('home')><a href="#">Home</a></li>
				<li @setActiveLink('tracked.most')><a href="#">Most Tracked</a></li>
				<li @setActiveLink('tracked.latest')><a href="#">Latest Added</a></li>
				<li @setActiveLink('search')><a href="#">Search</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a class="steam-small-login" href="#">Sign in through STEAM</a></li>
				<li><a class="heart-red" href="#"><span class="fa fa-heart"></span></a></li>
			</ul>
		</div>
	</div>
</nav>