@extends('base')
@include('user.search')

@section('head')
  {{ HTML::style('css/user/user.css') }}
  <script>userLoad = [];</script>
@stop

@section('title')
&mdash; @if(isset($hatedUsers)) Most Tracked Users
@elseif(isset($latestUserAdded)) Latest Added User
@endif
@stop

@section('content')
@section('search')
@show
<div class="col-md-8 col-md-offset-2">
  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th></th>
          <th><span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Alias of user added">User</span></th>
          <th class="text-center">
            <span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Valve Anti-Cheat / Overwatch Status (mm/dd/yy)">VAC / Overwatch</span>
          </th>
          <th class="text-center">
            <span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="# of people who has this person on list">Tracked By</span>
            </th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      @foreach ($vBanUsers as $vBanUser)
        <tr>
          @if(!is_object($vBanUser))
            <td colspan='7' id="user-{{{ bcsub($vBanUser, '76561197960265728') }}}" style="height: 49px" class="text-muted text-center"><script>userLoad.push({{{ bcsub($vBanUser, '76561197960265728') }}});</script><span class="icon-spin glyphicon glyphicon-refresh"></span> This user is currently loading</td>
          @else
            @include('user.userSlide', array('vBanUser' => $vBanUser))
          @endif
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@stop

@section('script')
  {{ HTML::script('js/user/userLoad.js') }}
@stop
