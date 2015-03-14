@extends('layout.app')

@section('content')
	<div id="profile" class="profile-page">
		<div class="profile-header">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-md-3 col-lg-2 col-lg-offset-1">
						<div class="profile-avatar">
							<img class="img-responsive" src="https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/60/60f1f401b4a9b2870486c1750d430d9d0f1d7369_full.jpg">
						</div>
					</div>
					<div class="col-xs-12 col-md-9">
						<div class="row">
							<div class="col-xs-12">
								<div class="profile-username">Jung - VacStat.us</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 col-md-2">
								<div class="profile-steam">
									<a href="#">
										<span class="fa fa-steam"></span>
									</a>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6 col-md-4">
								<ul class="profile-info">
									<li>
										<div class="row">
											<div class="col-xs-6 text-right"><strong>Creation</strong></div>
											<div class="col-xs-6">Jan 10 2010</div>
										</div>
									</li>
									<li>
										<div class="row">
											<div class="col-xs-6 text-right"><strong>Steam3 ID</strong></div>
											<div class="col-xs-6">U:1:60051399</div>
										</div>
									</li>
									<li>
										<div class="row">
											<div class="col-xs-6 text-right"><strong>Steam ID 32</strong></div>
											<div class="col-xs-6">STEAM_0:1:30025699</div>
										</div>
									</li>
									<li>
										<div class="row">
											<div class="col-xs-6 text-right"><strong>Steam ID 64</strong></div>
											<div class="col-xs-6">76561198020317127</div>
										</div>
									</li>
								</ul>
							</div>
							<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5">
								<ul class="profile-info">
									<li>
										<div class="row">
											<div class="col-xs-6 text-right"><strong>Profile Status</strong></div>
											<div class="col-xs-6"><div class="text-primary">Public</div></div>
										</div>
									</li>
									<li>
										<div class="row">
											<div class="col-xs-6 text-right"><strong>Vac Ban</strong></div>
											<div class="col-xs-6"><div class="text-success">Normal</div></div>
										</div>
									</li>
									<li>
										<div class="row">
											<div class="col-xs-6 text-right"><strong>Trade ban</strong></div>
											<div class="col-xs-6"><div class="text-success">Normal</div></div>
										</div>
									</li>
									<li>
										<div class="row">
											<div class="col-xs-6 text-right"><strong>Community Ban</strong></div>
											<div class="col-xs-6"><div class="text-success">Normal</div></div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop