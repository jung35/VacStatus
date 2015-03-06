<!DOCTYPE html>
<html>
<head>
	<title>VacStatus @yield('title')</title>
	@include('layout.head')
</head>
<body>
	@include('layout.header')
	
	@section('content')
	@show

	@include('layout.footer')

	@yield('js')
</body>
</html>