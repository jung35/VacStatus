	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<meta name="auth" content="{{ \Auth::check() }}" />
	<meta name="_token" content="{{ csrf_token() }}" />

    <meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1"/>
	<meta name="robots" content="index, follow" />
    <meta name="revisit-after" content="1 DAYS" />
    <meta name="author" content="Jung Oh"/>
    <meta itemprop="name" content="VacStatus" />
    <meta name="description" itemprop="description" content="Keep track of people's VAC (Valve anti-cheat) status in a list for games like Counter-Strike: Global Offensive, Counter-Strike: Source, and etc.. And subscribe email to receive notifications of any bans."/>
    <meta itemprop=image content="https://vacstat.us/favicon.png"/>
    <meta name="keywords" content="vac, status, vacstatus, vban, vbanstatus, vb, vs, vacstatus.com, vac.com, vban.com, vbanstatus.com, vac status, vban status, list, vac list, vac ban list, ban list, steam, cs, csgo, cs go, tf2 , tf, css, valve, hl, hl2, steam ban, steam ban list, valve anti-cheat, anti cheat, anti-cheat, valve cheat, hack, hacking, virus, wallhack, aimbot"/>

	<link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="/css/app.css">

	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script>
		$(function() {
			$.ajaxSetup({
				headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') }
			});
		});
	</script>
	<script src="/js/all.js"></script>