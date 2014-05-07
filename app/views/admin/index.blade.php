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
    <li class="active"><a href="{{{ URL::route('admin.index') }}}">Home</a></li>
    <li><a href="#">Profile</a></li>
    <li><a href="#">Messages</a></li>
  </ul>
  <div class="col-md-10">
    <div class="row">
      <div class="col-md-4">
        <h3 class="text-center">Site Info</h3>
        <ul class="admin-site-info">
          <li><b>Steam Users</b>: 123</li>
          <li><b>Subscribed Users</b>: 123</li>
          <li><b>Recorded Users</b>: 123</li>
          <li><b>Listed Users</b>: 123</li>
          <li><b>Avg. Users Per List</b>: 123</li>
          <li><b># Of Aliases Users</b>: 123</li>
          <li><b># Of Site News</b>: 123</li>
        </ul>
      </div>
      <div class="col-md-4">
        <h3 class="text-center">Test</h3>
      </div>
      <div class="col-md-4">
        <h3 class="text-center">Test</h3>
      </div>
    </div>
  </div>
</div>
@stop
