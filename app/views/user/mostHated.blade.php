@extends('base')
@include('user.search')

@section('head')
  <link rel="stylesheet" href="/css/user/user.css">
@stop

@section('title')
&mdash; Most Tracked Users
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
            <span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Valve Anti-Cheat Status (mm/dd/yy)">VAC</span>
          </th>
          <th class="text-center">
            <span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Community Ban Status">Community</span>
          </th>
          <th class="text-center">
            <span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="# of people who has this person on list">Tracked By</span>
            </th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      @foreach ($hatedUsers as $hatedUser)
      <tr>
        <td><img src="{{{ $hatedUser[1]->steam_avatar_url_small }}}"></td>
        <td>{{{ $hatedUser[1]->display_name }}}</td>
        @if($hatedUser[1]->vac_banned > -1)
        <td class="text-danger text-center"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;{{{ date('m/d/Y', time()-($hatedUser[1]->vac_banned*86400)) }}}</td>
        @else
        <td class="text-success text-center"><span class="glyphicon glyphicon-remove"></span></td>
        @endif
        @if($hatedUser[1]->community_banned)
        <td class="text-danger text-center"><span class="glyphicon glyphicon-ok"></span></td>
        @else
        <td class="text-success text-center"><span class="glyphicon glyphicon-remove"></span></td>
        @endif
        <td class="text-center">{{{ $hatedUser[0] }}}</td>
        <td><a href="{{ URL::route('user', array( $hatedUser[1]->community_id )) }}" target="_blank" type="button" class="btn btn-info btn-sm">Info</a></td>
        <td>
          @if (Session::get('user.in'))
            @if($hatedUser[1]->is_tracking)
              {{ Form::open(array('route' => 'remove')) }}
              {{ Form::hidden('vBanUserId', $hatedUser[1]->id) }}
              <input type="submit" class="btn btn-danger btn-sm" value="Delete">
              {{ Form::close() }}
            @else
              {{ Form::open(array('route' => 'add')) }}
              {{ Form::hidden('vBanUserId', $hatedUser[1]->id) }}
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
