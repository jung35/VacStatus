<!DOCTYPE html>
<html>
<head>
	<title>VacStatus Admin Panel</title>
	@include('layout.head')
</head>
<body>
	<div class="wrap">
		@include('layout.header')
		@include('admin.layout.header')
		
		@section('content')
		@show
		
		<div class="pushFooter"></div>
	</div>

	@include('layout.footer')

	@yield('js')
</body>
</html>