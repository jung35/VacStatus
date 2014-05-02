@extends('base')
@include('user.search')

@section('head')
  <link rel="stylesheet" href="/css/user/user.css">
@stop

@section('title')
&mdash; {{{ $userInfo->display_name }}}
@stop

@section('content')
@section('search')
@show
<div class="col-md-8 col-md-offset-2">
  <div class="row">
    <div class="col-md-4 text-center"><h3>{{{ $userInfo->display_name }}}</h3></div>
    @if ($userInfo->private_profile)
    <h3 class="col-md-4 text-danger text-center-md user-profile-status"><b>PRIVATE</b></h3>
    @endif
  </div>
  <div class="row">
    <div class="col-md-4 text-center">
      <img class="text-center user-avatar img-responsive" src="{{{ $userInfo->steam_avatar_url_big }}}">
      <br><br>
      <p><a href="http://steamcommunity.com/profiles/{{{ $userInfo->community_id }}}" target="_blank">Community Profile</a></p>
      <br>
      @if(Session::get('user.in'))
        @if($userInfo->is_tracking)
        {{ Form::open(array('route' => 'remove')) }}
        {{ Form::hidden('vBanUserId', $userInfo->id) }}
        <input type="submit" class="btn btn-danger btn-sm" value="Delete">
        {{ Form::close() }}
        @else
        {{ Form::open(array('route' => 'add')) }}
        {{ Form::hidden('vBanUserId', $userInfo->id) }}
        <input type="submit" class="btn btn-info btn-sm" value="Add">
        {{ Form::close() }}
        @endif
      @endif
    </div>
    <div class="col-md-8">
      <div class="row">
        <h4>User Info</h4>

        <div class="user-info-box col-md-6"><b>Community ID</b>: {{{ $userInfo->community_id }}}</div>

        <div class="user-info-box col-md-6"><b>Steam ID</b>: {{{ $userInfo->steam_id }}}</div>

        <div class="user-info-box col-md-6"><b class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Day Account was created on Steam (mm/dd/yy)">Account Creation</b>:
        @if ($userInfo->steam_creation == 0)
          <span class="text-danger">UNKNOWN</span>
        @else
        {{{ date('m/d/Y', $userInfo->steam_creation) }}}
        @endif
        </div>
        <div class="user-info-box col-md-6"><b class="ttip cursor" data-toggle="tooltip" data-placement="top" title="VAC Ban status (mm/dd/yy)">VAC Status</b>:
        @if($userInfo->vac_banned > -1)
        <span class="text-danger">BANNED ({{{ date('m/d/Y', time() - $userInfo->vac_banned * 86400) }}})</span>
        @else
          <span class="text-success">NORMAL</span>
        @endif
        </div>
        <div class="user-info-box col-md-6"><b class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Market Ban status">Market Status</b>:
        @if($userInfo->market_banned)
        <span class="text-danger">BANNED</span>
        @else
          <span class="text-success">NORMAL</span>
        @endif</div>

        <div class="user-info-box col-md-6"><b class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Community Ban status">Community Status</b>:
        @if($userInfo->community_banned)
        <span class="text-danger">BANNED</span>
        @else
          <span class="text-success">NORMAL</span>
        @endif</div>

        <div class="user-info-box col-md-12"><b class="ttip cursor" data-toggle="tooltip" data-placement="top" title="# of others that put this used in their list">Tracked By</b>: {{{ $userInfo->get_num_tracking == 1 ? '1 User': "{$userInfo->get_num_tracking} Users" }}}</div>
        <div class="row">
          <h4 class="col-md-12">Other Known Aliases</h4>
        </div>
        <table class="table user-other-alias">
          <thead>
            <tr>
              <th><span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Other known usernames">Alias</span></th>
              <th><span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Date when the alias was used (mm/dd/yy hh:mm am/pm)">Date</span></th>
            </tr>
          </thead>
          <tbody>
          @foreach ($userInfo->user_alias as $vBanUserAlias)
            <tr>
              <td>{{{ $vBanUserAlias->alias }}}</td>
              <td>{{{ date('m/d/Y', $vBanUserAlias->time_used) }}}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="col-md-8 col-md-offset-2">
  <div id="disqus_thread"></div>
  <script type="text/javascript">
    var disqus_shortname = 'vbanstatus';
    var disqus_identifier = '{{{ $userInfo->community_id }}}';
    var disqus_title = 'vBan Status [{{{ $userInfo->community_id }}}]';

    /* * * DON'T EDIT BELOW THIS LINE * * */
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
  </script>
</div>

@stop
