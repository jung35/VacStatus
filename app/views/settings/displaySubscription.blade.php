@extends('settings/layout')

@section('settings_content')
  <h3>Subscribed Lists<br><small>List of lists that you are subscribed to</small></h3>
  @foreach($subscription as $sub)
  <a style="display: inline-block;" href="{{{ URL::route('list_display', array( $sub->user_id, $sub->user_list_id )) }}}" class="panel">
    <h5>{{{ $sub->title }}}<br><small>{{{ $sub->display_name }}}</small></h5>
  </a>
  @endforeach

@stop
