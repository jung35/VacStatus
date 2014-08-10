<h3>{{ $title }}</h3>
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
      @if(isset($userList->personal) && $userList->personal)
        @include('list/listRow', array('UserListProfile' => $UserListProfile, 'personal' => true))
      @else
        @include('list/listRow', array('UserListProfile' => $UserListProfile))
      @endif
    @endforeach
  </tbody>
</table>
