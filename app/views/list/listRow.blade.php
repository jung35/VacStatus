<tr class="profileId_{{{ $UserListProfile->profile_id ?:$UserListProfile->id }}}">
  <td class="vacstatus-list-avatar list-replaceable">
    <img src="{{{ $UserListProfile->avatar_thumb }}}">
  </td>
  <td class="vacstatus-list-user list-replaceable">
    <a href="{{{ URL::route('profile', Array('steam3Id'=> Steam\Steam::toBigId($UserListProfile->small_id) )) }}}" target="_blank">
      <span {{ (is_numeric($UserListProfile->donation) && $UserListProfile->donation >= DonationPerk::getPerkAmount('green_name')) ? "class='text-success'" : '' }}>
        {{{ $UserListProfile->display_name }}}
      </span>
    </a>
    @if(!is_null($UserListProfile->profile_description) && $UserListProfile->profile_description != '')
    <a href="#" onClick="$('#profile_description').fadeToggle(250, function() { $(this).text($(this).text() == '(Description)' ? '{{{ $UserListProfile->profile_description }}}' : '(Description)').fadeToggle(250); });">
      <div id="profile_description">(Description)</div>
    </a>
    @endif
  </td>
  @if($UserListProfile->vac > 0)
  <td class="vacstatus-list-status list-replaceable text-alert">
    <span class="fa fa-check"></span>&nbsp;&nbsp;{{{ date('M j Y', time() - ($UserListProfile->vac_days * 24 * 60 * 60)) }}}
  </td>
  @else
  <td class="vacstatus-list-status list-replaceable text-success">
    <span class="fa fa-times"></span>
  </td>
  @endif
  <td class="vacstatus-list-tracker list-replaceable">{{{ $UserListProfile->get_num_tracking }}}</td>
  <td class="vacstatus-list-button">
    @if(Auth::check())
      @if(isset($personal) && $personal)
        <form action="{{{ URL::route('list_user_delete') }}}" method="POST">
          <input type="hidden" name="list_id" value="{{{ $UserListProfile->user_list_id }}}">
          <input type="hidden" name="profile_id" value="{{{ $UserListProfile->profile_id }}}">
          <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
          <button onClick="javascript:doDeleteUserList(this.form);" name="submit" type="button" class="button tiny alert-bg"><i class="fa fa-minus"></i></button>
        </form>
      @else
        <button class="button tiny" onClick="javascript:addUserList({{{ $UserListProfile->profile_id ?:$UserListProfile->id }}});"><i class="fa fa-plus"></i></button>
      @endif
    @endif
  </td>
</tr>
