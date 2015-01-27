<h3 class="small-6 columns list-title">

@if((isset($userList->custom) && $userList->custom) && (Auth::check() && isset($userMail)))
  @if($userMail == null || (!$userMail->canMail() && !$userMail->canPushbullet() && !$userMail->canPushover()))
    {{ Form::open(array('action' => 'SubscriptionController@store', 'class' => 'left list-subscribe', 'id' => 'listSubscribeForm')) }}
      <a data-tooltip aria-haspopup="true" class="has-tip" title="Please verify email inorder to subscribe!">
        <i class="fa fa-star text-alert"></i>
      </a>
    {{ Form::close() }}
  @else
    @if($subscription == null)
      {{ Form::open(array('action' => 'SubscriptionController@store', 'class' => 'left list-subscribe', 'id' => 'listSubscribeForm')) }}
        <input type="hidden" name="list_id" value="{{{ $userList->list_id }}}">
        <a data-tooltip aria-haspopup="true" class="has-tip" title="Subscribe" onClick="javascript:document.getElementById('listSubscribeForm').submit();false;">
          <i class="fa fa-star-o"></i>
        </a>
      {{ Form::close() }}
    @else
      {{ Form::open(array('method' => 'DELETE', 'action' => array('SubscriptionController@destroy', $userList->list_id), 'class' => 'left list-subscribe', 'id' => 'listSubscribeForm')) }}
        <a data-tooltip aria-haspopup="true" class="has-tip" title="un-Subscribe" onClick="javascript:document.getElementById('listSubscribeForm').submit();false;">
          <i class="fa fa-star text-success"></i>
        </a>
      {{ Form::close() }}
    @endif
  @endif
@endif

<span class="actual-list-title">{{{ isset($userList->title) ? $userList->title : 'List' }}}</span>
@if(isset($userList->user_name) && $userList->user_name)
<small>{{{ $userList->user_name }}}</small>
@endif
</h3>
<h3 class="small-6 columns text-right list-options">
@if((isset($userList->custom) && $userList->custom) && (isset($userList->privacy) && $userList->privacy < 3))
    <a {{ $userList->privacy == 2 ? 'class="text-alert"' : '' }} onClick="javascript:showListLink({{{ $userList->user_id }}}, {{{ $userList->list_id }}})"><i class="fa fa-link"></i></a>
@endif
@if(isset($userList->personal) && $userList->personal)
  <a onClick="javascript:showEditForm({{{ $userList->list_id }}}, {{{ $userList->privacy }}});"><i class="fa fa-pencil text-success"></i></a>
@endif
</h3>
<table width="100%">
  <thead>
    <tr>
      <th class="vacstatus-list-avatar show-for-medium-up"></th>
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
@if(isset($userList->update))
<script>
  var userToUpdate = [{{ implode(',', $userList->update) }}];
  if(typeof userMultiUpdate != 'undefined') {
    userMultiUpdate(userToUpdate);
  }
</script>
@endif
