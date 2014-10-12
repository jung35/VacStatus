@extends('layout')

@section('content')
    <div class="large-12 column">
      <h2>Admin Control Panel</h2>
    </div>
    <ul class="large-3 medium-3 column side-nav">
      <li><a href="{{{ URL::route('admin_home') }}}">Admin Home</a></li>
      <li><a href="{{{ URL::route('admin_news') }}}">News &amp; Update</a></li>
      <li><a href="#">View Logs</a></li>
    </ul>
    <div class="large-9 medium-9 column" style="margin-top: 14px;">
      <div class="row">
        @section('subcontent')
        @show
      </div>
  </div>
@stop
