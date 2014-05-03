@extends('emails.emailBase')

@section('title')
Found {{{ count($bannedUsers) }}} Users
@stop

@section('emailContent')
<h1>Hackers Found!</h1>
<h3>Notification to finding hacker :)</h3>
You were right! We found {{{ count($bannedUsers) }}} of those who were on your list that were hackers.
<table width="100%">
  <tr>
    <td></td>
    <td>Player Name</td>
    <td>Added Date</td>
  </tr>
  @foreach($bannedUsers as $bannedUser)
  <tr>
    <td><img src="{{{ $bannedUser->vBanUser->steam_avatar_url_small }}}"></td>
    <td>{{{ $bannedUser->vBanUser->display_name }}}</td>
    <td>{{{ date('m/d/Y', strtotime($bannedUser->created_at)) }}}</td>
  </tr>
  @endforeach
</table>
@stop
