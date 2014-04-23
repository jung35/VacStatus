@extends('base')

@section('head')
<link rel="stylesheet" href="./css/index.css">
@stop

@section('content')
    <img src="./img/screen.png" class="example-screen img-responsive">
    <div id="carousel-generic" class="carousel slide" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <li data-target="#carousel-generic" data-slide-to="0" class="active"></li>
        <li data-target="#carousel-generic" data-slide-to="1"></li>
        <li data-target="#carousel-generic" data-slide-to="2"></li>
        <li data-target="#carousel-generic" data-slide-to="3"></li>
      </ol>

      <!-- Wrapper for slides -->
      <div class="carousel-inner">
        <div class="item active">
          <img src="./img/example.png" class="img-responsive">
        </div>
        <div class="item">
          <img src="./img/example2.png" class="img-responsive">
        </div>
        <div class="item">
          <img src="./img/example3.png" class="img-responsive">
        </div>
        <div class="item">
          <img src="./img/example4.png" class="img-responsive">
        </div>
      </div>

      <!-- Controls -->
      <a class="left carousel-control" href="#carousel-generic" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left"></span>
      </a>
      <a class="right carousel-control" href="#carousel-generic" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right"></span>
      </a>
    </div>
    <h1 class="text-center">vBan Status</h1>
    <p class="h1-sub text-center">Keep track of people's VAC ban status in a list</p>
    <p class="text-center"><a href="{{ action('HomeController@steamLogin') }}" type="button" class="btn btn-info btn-lg">Login with Steam</a></p>
@stop
