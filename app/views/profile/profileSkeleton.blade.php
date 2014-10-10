<div class="vacstatus-profile">
  <div class="medium-2 small-12 columns avatar">
    @if(Auth::check())
    <a class="profile-adduser" onClick="javascript:addUserList({{{ $profile->getId() }}});"><i class="fa fa-plus"></i></a>
    @endif
    <img src="{{{ $profile->getAvatar() }}}">
  </div>
  <div class="medium-10 small-12 columns basic small-only-text-center">
    @if($profile->site_admin)
    <div class="label warning profile-label">Superstar</div>
    @endif
    @if($profile->isPrivate())
    <div class="label alert profile-label">Private</div>
    @endif
    @if(is_numeric($profile->donation) && $profile->donation >= 1)
    <div class="label success profile-label">Donator</div>
    @endif
    <h3>{{{ $profile->getDisplayName() }}}<!-- <small>&nbsp;</small> --></h3>
    <div class="row">
      <div class="medium-2 columns">
        <span class="big-steam"><a href="http://steamcommunity.com/profiles/{{{ $profile->getSteam3Id() }}}" target="_blank"><i class="fa fa-steam"></i></a></span>
      </div>
      <div class="medium-5 columns detailed">
        <div class="row profile-detail">
          <div class="medium-5 columns profile-type small-only-text-center">Creation</div>
          <div class="medium-7 columns">{{{ $profile->getSteamCreation() }}}</div>
        </div>
        <div class="row profile-detail">
          <div class="medium-5 columns profile-type small-only-text-center">STEAM2 ID</div>
          <div class="medium-7 columns">{{{ $profile->getSteam2Id() }}}</div>
        </div>
        <div class="row profile-detail">
          <div class="medium-5 columns profile-type small-only-text-center">STEAM3 ID</div>
          <div class="medium-7 columns">{{{ $profile->getSteam3Id() }}}</div>
        </div>
      </div>
      <div class="medium-5 columns detailed">
        <div class="row profile-detail">
          <div class="medium-6 columns profile-type small-only-text-center">VAC</div>
          <div class="medium-6 columns {{{ $profile->ProfileBan->isVacBanned() ? 'text-alert' : 'text-success' }}}">{{{ $profile->ProfileBan->isVacBanned() ? 'Banned' : 'Normal' }}}</div>
        </div>
        <div class="row profile-detail">
          <div class="medium-6 columns profile-type small-only-text-center">Market</div>
          <div class="medium-6 columns {{{ $profile->ProfileBan->isTradeBanned() ? 'text-alert' : 'text-success' }}}">{{{ $profile->ProfileBan->isTradeBanned() ? 'Banned' : 'Normal' }}}</div>
        </div>
        <div class="row profile-detail">
          <div class="medium-6 columns profile-type small-only-text-center">Community</div>
          <div class="medium-6 columns {{{ $profile->ProfileBan->isCommunityBanned() ? 'text-alert' : 'text-success' }}}">{{{ $profile->ProfileBan->isCommunityBanned() ? 'Banned' : 'Normal' }}}</div>
        </div>
      </div>
      <!-- <div class="medium-5 columns"></div> -->
    </div>
  </div>

  <div class="medium-12 columns">
    <hr>
  </div>

  <div class="medium-12 columns detailed">
    <h4 class="small-only-text-center">VAC Ban</h4>
    <ul class="large-6 large-centered columns">

      <li class="large-6 medium-6 columns profile-detail small-only-text-center">
        <ul class="row">
          <li class="medium-6 columns text-right profile-type small-only-text-center"># of Bans</li>
          <li class="medium-6 columns">{{{ $profile->ProfileBan->getVac() }}}</li>
        </ul>
      </li>
      <li class="large-6 medium-6 columns profile-detail small-only-text-center">
        <ul class="row">
          <li class="medium-6 columns text-right profile-type small-only-text-center">Last Ban</li>
          <li class="medium-6 columns">{{{ $profile->ProfileBan->getVacDays() }}}</li>
        </ul>
      </li>

    </ul>
  </div>

  <div class="medium-12 columns">
    <hr>
  </div>

  <div class="medium-12 columns detailed">
    <h4 class="small-only-text-center">User Aliases</h4>
    <div class="medium-6 columns">
      <table width="100%">
        <thead>
          <tr>
            <th colspan="2" class="text-center">Alias History</th>
          </tr>
          <tr>
            <th width="170px">Used On</th>
            <th>Username</th>
          </tr>
        </thead>
        <tbody>
        @foreach($profile->ProfileOldAlias()->orderBy('id', 'DESC')->get() as $alias)
          <tr>
            <td>{{{ date('M j Y, g:i a', $alias->getTime()) }}}</td>
            <td>{{{ $alias->getAlias() }}}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="medium-6 columns">
      <table width="100%">
        <thead>
          <tr>
            <th colspan="2" class="text-center">Recent Aliases</th>
          </tr>
          <tr>
            <th width="170px">Used On</th>
            <th>Username</th>
          </tr>
        </thead>
        <tbody>
          @foreach($profile->getAlias() as $alias)
          <tr>
            <td>{{{ Steam\SteamUser::cleanAliasDate($alias->timechanged) }}}</td>
            <td>{{{ $alias->newname }}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="medium-12 columns">
    <hr>
  </div>

  <div class="medium-12 columns detailed">
    <h4 class="small-only-text-center">VacStatus Info</h4>
    <ul class="row">

      <li class="large-4 medium-6 small-6 columns profile-detail small-only-text-center">
        <ul class="row">
          <li class="medium-6 columns text-right profile-type small-only-text-center">First Checked</li>
          <li class="medium-6 columns">{{{ date('M j Y', strtotime($profile->created_at)) }}}</li>
        </ul>
      </li>

      <li class="large-4 medium-6 small-6 columns profile-detail small-only-text-center">
        <ul class="row">
          <li class="medium-6 columns text-right profile-type small-only-text-center">Times Checked</li>
          <li class="medium-6 columns">{{{ $old_check[0] }}} <small>({{{ $old_check[1] ? date('M j Y', $old_check[1]) : 'NEVER'}}})</small></li>
        </ul>
      </li>

      <li class="large-4 medium-6 small-6 columns profile-detail small-only-text-center">
        <ul class="row">
          <li class="medium-6 columns text-right profile-type small-only-text-center">Times Added</li>
          <li class="medium-6 columns">{{{ $profile->getCount }}} <small>({{{ date('M j Y', $profile->lastCount) }}})</small></li>
        </ul>
      </li>

    </ul>
  </div>

  <div class="medium-12 columns">
    <hr>
  </div>

  <div class="medium-12 columns">
    <div id="disqus_thread"></div>
    <br><!--lol this line break-->
    <script type="text/javascript">
      var disqus_shortname = 'vbanstatus';
      var disqus_identifier = '{{{ $profile->getSteam3Id() }}}';
      var disqus_title = 'VacStatus [{{{ $profile->getSteam3Id() }}}]';

      /* * * DON'T EDIT BELOW THIS LINE * * */
      (function() {
          var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
          dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
          (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
      })();
    </script>
  </div>
</div>
