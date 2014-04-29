@extends('base')
@section('title')
&mdash; Subscribe
@stop

@section('content')
  <h2>Track Your List By E-Mail<br><small>We will check every few hours to see if the person you added to your list has been VAC Banned since the time you added them</small></h2>

  @if(isset($userMail->verify))
    @if ($userMail->verify != 'done')
      <p class="bg-info">It looks like you haven't confirmed your email yet. Please check you inbox and spam! <a href="{{{ URL::route('resendEmail') }}}">Send verification mail!</p>
    @elseif($userMail->verify == 'done')
      <p class="bg-info">You're all done! You're currently being tracked and all your email will be sent to {{{ $userMail->email }}}</p>
    @endif
  @endif
  <br>

  {{ Form::open(array('action' => 'subscribe', 'class' => 'col-xs-12')) }}
    <div class="input-group input-group-lg col-xs-12">
      {{ Form::email('setEmail', isset($userMail->verify) ? $userMail->email : null, array('class' => 'form-control', 'placeholder' => 'Email')) }}
    </div>
    <br>
    <input type="submit" value="Subscribe Me!" class="btn btn-primary btn-lg btn-block">
  {{ Form::close() }}
  @if(isset($userMail->verify))
    {{ Form::open(array('action' => 'subscribe', 'class' => 'col-xs-12')) }}
      {{ Form::hidden('unsub', 1) }}
      <br>
      <input type="submit" value="Unsubscribe" class="btn btn-danger btn-lg btn-block">
    {{ Form::close() }}
  @endif
@stop
