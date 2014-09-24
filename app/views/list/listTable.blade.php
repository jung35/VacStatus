<h3 class="small-6 columns list-title">{{{ $userList->title }}}
@if(isset($userList->user_name) && $userList->user_name)
<small>{{{ $userList->user_name }}}</small>
@endif
</h3>
<h3 class="small-6 columns text-right list-options">
@if(isset($userList->custom) && $userList->custom && isset($userList->privacy) && $userList->privacy < 3)
  <a {{ $userList->privacy == 2 ? 'class="text-alert"' : '' }} onClick="javascript:showListLink({{{ $userList->user_id }}}, {{{ $userList->list_id }}})"><i class="fa fa-link"></i></a>
@endif
@if(isset($userList->personal) && $userList->personal)
  <a onClick="javascript:showEditForm({{{ $userList->list_id }}}, {{{ $userList->privacy }}});"><i class="fa fa-pencil text-success"></i></a>
@endif
</h3>
<table>
  <thead>
    <tr>
      <th class="vacstatus-list-avatar"></th>
      <th class="vacstatus-list-user">User</th>
      <th class="vacstatus-list-status">VAC</th>
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
<script>
  var userToUpdate = [{{ implode(',', $userList->update) }}];
  if(typeof userMultiUpdate != 'undefined') {
    userMultiUpdate(userToUpdate);
  }
</script>
