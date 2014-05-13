@extends('base')

@section('title')
&mdash; Admin CP
@stop

@section('head')
<link rel="stylesheet" href="{{{ URL::route('home') }}}/css/admin/index.css">
@stop

@section('content')
<div class="row">
  <ol class="breadcrumb">
    <li><a>Admin Panel</a></li>
    <li class="active">Home</li>
  </ol>
</div>
<div class="row">
  <ul class="col-md-2 nav nav-pills nav-stacked">
    <li><a href="{{{ URL::route('admin.index') }}}">Home</a></li>
    <li><a href="#">Profile</a></li>
    <li><a href="#">Messages</a></li>
  </ul>
  <div class="col-md-10">
    <div class="row">
      @section('adminContent')
      @show
    </div>
  </div>
</div>
@stop
