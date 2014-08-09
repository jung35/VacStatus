
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
      @include('list/listRow', array('UserListProfile' => $UserListProfile))
    @endforeach
  </tbody>
</table>
