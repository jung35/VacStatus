@extends('base')

@section('title')
&mdash; {{ $message[0] }}
@stop

@section('content')
  <h1 class="text-center">{{{ $message[1] }}}<br><br><small>{{{ $type }}} :(</small></h1>
@stop
