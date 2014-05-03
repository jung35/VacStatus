@extends('base')
@include('user.search')

@section('head')
  <link rel="stylesheet" href="/css/user/user.css">
@stop

@section('title')
@if(isset($searching))
&mdash; Search
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
          @if (!isset($searching))
          <th>
            <span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Date added to list (mm/dd/yy)">Date</span>
          </th>
          @endif
          <th class="text-center">
            <span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="Valve Anti-Cheat / Overwatch Status (mm/dd/yy)">VAC / Overwatch</span>
          </th>
          <th class="text-center">
            <span class="ttip cursor" data-toggle="tooltip" data-placement="top" title="# of others who also has this person on list">Others</span>
            </th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      @if ((isset($vBanCount) && $vBanCount > 0) || (method_exists($vBanList, 'count') && $vBanList->count() > 0))
        @foreach ($vBanList as $vBanUser)
        <tr>
          <td><img src="{{{ $vBanUser->vBanUser->steam_avatar_url_small }}}"></td>
          <td>{{{ $vBanUser->vBanUser->display_name }}}</td>
          @if (!isset($searching))
          <td>{{{ date('m/d/Y', strtotime($vBanUser->created_at)) }}}</td>
          @endif
          @if($vBanUser->vBanUser->vac_banned > -1)
          <td class="text-danger text-center"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;{{{ date('m/d/Y', time()-($vBanUser->vBanUser->vac_banned*86400)) }}}</td>
          @else
          <td class="text-success text-center"><span class="glyphicon glyphicon-remove"></span></td>
          @endif
          <td class="text-center">{{{ vBanList::wherevBanUserId($vBanUser->vBanUser->id)->count()+($vBanUser->vBanUser->is_tracking ? -1: 0) }}}</td>
          <td><a href="{{ URL::route('user', array( $vBanUser->vBanUser->community_id )) }}" target="_blank" type="button" class="btn btn-info btn-sm">Info</a></td>
          <td>
            @if (Session::get('user.in'))
              @if (isset($searching))
                @if($vBanUser->vBanUser->is_tracking)
                  {{ Form::open(array('route' => 'remove', 'target' => '_blank')) }}
                  {{ Form::hidden('vBanUserId', $vBanUser->vBanUser->id) }}
                  <input type="submit" class="btn btn-danger btn-sm" value="Delete">
                  {{ Form::close() }}
                @else
                  {{ Form::open(array('route' => 'add', 'target' => '_blank')) }}
                  {{ Form::hidden('vBanUserId', $vBanUser->vBanUser->id) }}
                  <input type="submit" class="btn btn-info btn-sm" value="Add">
                  {{ Form::close() }}
                @endif
              @else
                {{ Form::open(array('route' => 'remove')) }}
                  {{ Form::hidden('vBanUserId', $vBanUser->vBanUser->id) }}
                  <input type="submit" class="btn btn-danger btn-sm" value="Delete">
                {{ Form::close() }}
              @endif
            @endif
          </td>
        </tr>
        @endforeach
      @else
        <tr>
          <td colspan='7' class="text-muted text-center">No one is on your list :(</td>
        </tr>
      @endif
      </tbody>
    </table>
  </div>
  @if (!isset($searching))
    {{ $vBanList->links() }}
  @endif
</div>
@stop
