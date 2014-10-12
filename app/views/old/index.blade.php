@extends('layout')

@section('title')
Old Data
@stop

@section('content')
  @foreach($steamIds as $steamId)
  {{{ $steamId }}}<br>
  @endforeach
@stop

@section('javascript')
@stop
