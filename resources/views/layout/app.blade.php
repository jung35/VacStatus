<!DOCTYPE html>
<html>
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
</body>
</html>