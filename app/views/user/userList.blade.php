@extends('base')
@include('user.search')

@section('head')
  <link rel="stylesheet" href="/css/user/user.css">
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
        <td><img src="{{{ $vBanUser->steam_avatar_url_small }}}"></td>
        <td>{{{ $vBanUser->display_name }}}</td>
        @if($vBanUser->vac_banned > -1)
        <td class="text-danger text-center"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;{{{ date('m/d/Y', time()-($vBanUser->vac_banned*86400)) }}}</td>
        @else
        <td class="text-success text-center"><span class="glyphicon glyphicon-remove"></span></td>
        @endif
        <td class="text-center">{{{ $vBanUser->get_num_tracking }}}</td>
        <td><a href="{{ URL::route('user', array( $vBanUser->community_id )) }}" target="_blank" type="button" class="btn btn-info btn-sm">Info</a></td>
        <td>
          @if (Session::get('user.in'))
            @if($vBanUser->is_tracking)
              {{ Form::open(array('route' => 'remove')) }}
              {{ Form::hidden('vBanUserId', $vBanUser->id) }}
              <input type="submit" class="btn btn-danger btn-sm" value="Delete">
              {{ Form::close() }}
            @else
              {{ Form::open(array('route' => 'add')) }}
              {{ Form::hidden('vBanUserId', $vBanUser->id) }}
              <input type="submit" class="btn btn-info btn-sm" value="Add">
              {{ Form::close() }}
            @endif
          @endif
        </td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@stop
