@foreach($userList as $UserListProfile)
  @if(is_object($UserListProfile))
  <table class="for_profileId_{{{ $UserListProfile->profile_id }}}">
  <tr>
    <td class="vacstatus-list-avatar list-replaceable">
      <img src="{{{ $UserListProfile->avatar_thumb }}}">
    </td>
    <td class="vacstatus-list-user list-replaceable"><a href="{{{ URL::route('profile', Array('steam3Id'=> Steam\Steam::toBigId($UserListProfile->small_id) )) }}}" target="_blank"><span {{ (is_numeric($UserListProfile->donation) && $UserListProfile->donation >= 7.5) ? "class='text-success'" : '' }}>{{{ $UserListProfile->display_name }}}</span></a></td>
    @if($UserListProfile->vac > 0)
    <td class="vacstatus-list-status list-replaceable text-alert">
      <span class="fa fa-check"></span>&nbsp;&nbsp;03/19/2014
    </td>
    @else
    <td class="vacstatus-list-status list-replaceable text-success">
      <span class="fa fa-times"></span>
    </td>
    @endif
    <td class="vacstatus-list-tracker">{{{ $UserListProfile->get_num_tracking }}}</td>
  </tr>
  </table>
  @endif
@endforeach
