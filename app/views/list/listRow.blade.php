<tr class="profileId_{{{ $UserListProfile->profile_id ?:$UserListProfile->id }}}">
  <td class="vacstatus-list-avatar list-replaceable show-for-medium-up">
    <img src="{{{ $UserListProfile->avatar_thumb }}}">
  </td>
  <td class="vacstatus-list-user list-replaceable">
    <a href="{{{ URL::route('profile', Array('steam3Id'=> Steam\Steam::toBigId($UserListProfile->small_id) )) }}}" target="_blank">
      @if(!is_null($UserListProfile->profile_description) && !empty($UserListProfile->profile_description))
        <i class="fa fa-eye" data-tooltip aria-haspopup="true" class="has-tip" title="{{{ $UserListProfile->profile_description }}}"></i>
      @endif
      <span {{ (is_numeric($UserListProfile->donation) && $UserListProfile->donation >= DonationPerk::getPerkAmount('green_name')) ? "class='text-success'" : '' }}>
        {{{ $UserListProfile->display_name }}}
      </span>
    </a>
  </td>
  @if($UserListProfile->vac > 0)
  <td class="vacstatus-list-status list-replaceable text-alert">
    <div>
      <span class="fa fa-check show-for-small-only"></span>
      <span class="show-for-medium-up">{{{ date_format(new DateTime($UserListProfile->vac_banned_on), 'M j Y') }}}</span>
    </div>
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
