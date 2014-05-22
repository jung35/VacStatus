@if(!empty($vBanUser) && is_object($vBanUser) && isset($searching))
  @if(time() - strtotime($vBanUser->updated_at) > 3600)
    <td class="userList-refreshing" id="user-{{{ bcsub($vBanUser->community_id, '76561197960265728') }}}"><span class="icon-spin glyphicon glyphicon-refresh"></span></td>
    <script>userLoad.push({{{ bcsub($vBanUser->community_id, '76561197960265728') }}});</script>
  @else
    <td id="user-{{{ bcsub($vBanUser->community_id, '76561197960265728') }}}"><img src="{{{ $vBanUser->steam_avatar_url_small }}}"></td>
  @endif
  <td>{{{ $vBanUser->display_name }}}</td>
  @if (($searching == 'false' || $searching == false) && isset($displayAdded) && ($displayAdded != 'false' && $displayAdded != false))
  <td>{{{ date('m/d/Y', strtotime($vBanUser->created_at)) }}}</td>
  @endif
  @if($vBanUser->vac_banned > -1)
  <td class="text-danger text-center"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;{{{ date('m/d/Y', time()-($vBanUser->vac_banned*86400)) }}}</td>
  @else
  <td class="text-success text-center"><span class="glyphicon glyphicon-remove"></span></td>
  @endif
  <td class="text-center">{{{ vBanList::wherevBanUserId($vBanUser->id)->count()+(isset($vBanUser->is_tracking) && $vBanUser->is_tracking ? -1: 0) }}}</td>
  <td><a href="{{ URL::route('user', array( $vBanUser->community_id )) }}" target="_blank" type="button" class="btn btn-info btn-sm">Info</a></td>
  <td>
    @if(Session::get('user.in'))
      @if(isset($vBanUser->is_tracking) && $vBanUser->is_tracking)
        @if($searching == 'true' || $searching)
          {{ Form::open(array('route' => 'remove', 'target' => '_blank', 'onclick' => 'javascript:changeFormButton(\''.bcsub($vBanUser->community_id, '76561197960265728').'\');')) }}
        @else
          {{ Form::open(array('route' => 'remove')) }}
        @endif
        {{ Form::hidden('vBanUserId', $vBanUser->id) }}
        <input type="submit" class="btn btn-danger btn-sm" value="Delete">
        {{ Form::close() }}
      @else
        @if($searching == 'true' || $searching)
          {{ Form::open(array('route' => 'add', 'target' => '_blank', 'onclick' => 'javascript:changeFormButton(\''.bcsub($vBanUser->community_id, '76561197960265728').'\');')) }}
        @else
          {{ Form::open(array('route' => 'add')) }}
        @endif
        {{ Form::hidden('vBanUserId', $vBanUser->id) }}
        <input type="submit" class="btn btn-info btn-sm" value="Add">
        {{ Form::close() }}
      @endif
    @endif
  </td>
@endif
