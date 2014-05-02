@extends('emails.emailBase')

@section('title')
You're Almost Done!
@stop

@section('emailContent')
<h1>You're Almost Done!</h1>
<h3>Thank you for subscribing :)</h3>
Now the final step is to verify your mail and you're in! The list that you made will be tracked!
<br>
Please click to verify: <a href="{{{ URL::route('verify', $verify) }}}" target="_blank">{{{ URL::route('verify', $verify) }}}</a>
@stop
