@extends('layout.app')

@section('title')
&mdash; Profile
@stop

@section('content')
	<div id="profile" class="profile-page" data-steam64bitid="{{ $steam64BitId }}">
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
								<div class="profile-username">
									<a href="#"><span class="fa fa-plus faText-align"></span></a>
									<span class="beta-name donator-name admin-name">Jung - VacStat.us</span>
								</div>
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
		<div class="profile-badge">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<div class="label label-warning">Admin</div>
						<div class="label label-success">Donator</div>
						<div class="label label-primary">Beta</div>
					</div>
				</div>
			</div>
		</div>
		<div class="profile-body">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<div class="title">
							User Aliases
						</div>
					</div>
					<div class="col-xs-12 col-md-6 col-lg-5 col-lg-offset-1">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th colspan="2">Alias History</th>
									</tr>
									<tr>
										<th>Used On</th>
										<th>Username</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Mar 4 2015, 5:11 pm</td>
										<td>Jungㅁ</td>
									</tr>
									<tr>
										<td>Oct 11 2014, 9:46 pm</td>
										<td>Jung - VacStat.us</td>
									</tr>
									<tr>
										<td>Oct 11 2014, 9:14 pm</td>
										<td>Jung</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-xs-12 col-md-6 col-lg-5">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th colspan="2">Recent Aliases</th>
									</tr>
									<tr>
										<th>Used On</th>
										<th>Username</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Mar 9 2015, 10:57 am</td>
										<td>Jung</td>
									</tr>
									<tr>
										<td>Mar 8 2015, 10:44 pm</td>
										<td>PewPEw</td>
									</tr>
									<tr>
										<td>Mar 4 2015, 5:10 pm</td>
										<td>Jungㅁ</td>
									</tr>
									<tr>
										<td>Feb 27 2015, 8:41 am</td>
										<td>:~)</td>
									</tr>
									<tr>
										<td>Feb 21 2015, 12:49 pm</td>
										<td>lgimk</td>
									</tr>
									<tr>
										<td>Feb 3 2015, 12:23 pm</td>
										<td>Jung - VacStat.us</td>
									</tr>
									<tr>
										<td>Jan 24 2015, 6:33 pm</td>
										<td>asdf</td>
									</tr>
									<tr>
										<td>Jan 17 2015, 1:15 pm</td>
										<td>Jung - VacStat.us #FiatSquad</td>
									</tr>
									<tr>
										<td>Jan 15 2015, 6:56 am</td>
										<td>..</td>
									</tr>
									<tr>
										<td>Jan 10 2015, 3:48 pm</td>
										<td>Jung [Proffessionslserfer]</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<hr class="divider">
				<div class="row">
					<div class="col-xs-12 col-md-2 col-md-offset-2">
						<h3 class="title">Extra Info</h3>
						<div class="content text-center">
							<div class="row">
								<div class="col-xs-6 col-md-12">
									<strong># of VAC Bans</strong><br>
										0
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<h3 class="title">VacStatus Info</h3>
						<div class="content text-center">
							<div class="row">
								<div class="col-xs-6 col-md-4">
									<strong>First Checked</strong><br>
										Oct 12 2014
								</div>
								<div class="col-xs-6 col-md-4">
									<strong>Times Checked</strong><br>
										53 <sub>(Feb 2 2015)</sub>
								</div>
								<div class="col-xs-12 col-md-4">
									<strong>Times Added</strong><br>
										53 <sub>(Feb 2 2015)</sub>
								</div>
							</div>
						</div>
					</div>
					<hr class="divider">
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
				<div id="disqus_thread" class="disqus_thread"></div>
			</div>
		</div>
	</div>
@stop

@section('js')
	<script src="/js/pages/profile.js"></script>
	<script type="text/javascript">
		var disqus_shortname = 'vbanstatus';
		var disqus_identifier = '{{ $steam64BitId }}';
		var disqus_title = 'VacStatus [{{ $steam64BitId }}]';

		/* * * DON'T EDIT BELOW THIS LINE * * */
		(function() {
			var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
			dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
			(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();
	</script>
@stop