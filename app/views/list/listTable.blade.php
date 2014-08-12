<h3 class="small-6 columns">{{{ $userList->title }}}</h3>
@if(isset($userList->custom) && $userList->custom && isset($userList->privacy) && $userList->privacy == 1)
<h3 class="small-6 columns text-right"><a onClick="javascript:showListLink({{{ $userList->user_id }}}, {{{ $userList->list_id }}})"><i class="fa fa-link"></i></a></h3>
@endif
<table>
  <thead>
    <tr>
      <th class="vacstatus-list-avatar"></th>
      <th class="vacstatus-list-user">User</th>
      <th class="vacstatus-list-status">VAC / Overwatch</th>
      <th class="vacstatus-list-tracker">Tracked</th>
      <th class="vacstatus-list-button"></th>
    </tr>
  </thead>
  <tbody>
    @foreach($userList as $UserListProfile)
      @if(is_object($UserListProfile))
        @if(isset($userList->personal) && $userList->personal)
          @include('list/listRow', array('UserListProfile' => $UserListProfile, 'personal' => true))
        @else
          @include('list/listRow', array('UserListProfile' => $UserListProfile))
        @endif
      @endif
    @endforeach
  </tbody>
</table>
