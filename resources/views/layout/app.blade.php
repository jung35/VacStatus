<!DOCTYPE html>
<html lang="en">
<head>
	<title>
		VacStatus @yield('title')
	</title>
	
	@include('layout.head')
</head>
<body>
	<div class="wrap">
		@include('layout.header')
		
		@section('content')
		@show
		
		<div class="pushFooter"></div>
	</div>

	@include('layout.footer')

	@yield('js')

	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-50795838-1', 'auto');
		ga('send', 'pageview');
	</script>
</body>
</html>