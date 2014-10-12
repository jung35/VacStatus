<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1"/>
    <meta name="author" content="Jung Oh"/>
    <meta itemprop="name" content="VacStatus" />
    <meta name="description" itemprop="description" content="In a game like Counter-Strike: Global Offensive, you do not get a notification of some sort when the hacker gets banned. With VacStatus, you can now can keep in check of the possible hacker you reported!"/>
    <meta itemprop=image content="http://vacstatus.com/favicon.png"/>
    <meta name="keywords" content="vac, status, vacstatus, vban, vbanstatus, vb, vs, vacstatus.com, vac.com, vban.com, vbanstatus.com, vac status, vban status, list, vac list, vac ban list, ban list, steam, cs, csgo, cs go, tf2 , tf, css, valve, hl, hl2, steam ban, steam ban list, valve anti-cheat, anti cheat, anti-cheat, valve cheat"/>
    <meta name="robots" content="All" />
    <meta name="revisit-after" content="1 DAYS" />

    <link rel="stylesheet" href="/css/app.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.3.1/js/vendor/modernizr.min.js"></script>
    <script>
      var _token = '{{{ csrf_token() }}}';
    </script>

    <title>
      VacStatus &mdash;
      @section('title')
      Keep track of people's VAC status in a list
      @show
    </title>
  </head>
  <body>
    @if(Auth::check())
    <div id="addProfileUser" class="reveal-modal tiny" data-reveal>
      <h2 class="text-center">Add User to List</h2>
      <form action="{{{ URL::route('list_user_add') }}}" method="POST">
        <div class="row">
          <div class="large-12 columns">
            <label><strong>Add User into:</strong>
              <select name="list_id">
                @foreach(Auth::User()->UserList()->orderBy('id', 'DESC')->get() as $UserList)
                <option value="{{{ $UserList->getId() }}}">{{{ $UserList->getTitle() }}}</option>
                @endforeach
              </select>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
            <input type="hidden" name="profile_id" id="profile_id">
            <button type="button" name="submit" onClick="javascript:doAddUserList(this.form);" class="button expand">Add To List</button>
          </div>
        </div>
        <div class="row">
          <p class="large-12 columns">
            <a data-reveal-id="addList">Create a List</a>
          </p>
        </div>
      </form>
      <a class="close-reveal-modal">&#215;</a>
    </div>
    <div id="addList" class="reveal-modal tiny" data-reveal>
      <h2 class="text-center">Add List <small>(Limit {{{ Auth::User()->unlockList() }}})</small></h2>
      <form action="{{{ URL::route('list_add') }}}" method="POST">
        <div class="row">
          <div class="large-12 columns">
            <label><strong>List Privacy</strong>
              <select name="privacy">
                <option value="1">Public</option>
                <option value="2">Friends Only</option>
                <option value="3">Private</option>
              </select>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <label><strong>List Title</strong>
              <input type="text" placeholder="Fancy Title" name="title">
            </label>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
            <button type="button" name="submit" onClick="javascript:doCreateList(this.form);" class="button expand">Add List</button>
          </div>
        </div>
      </form>
      <a class="close-reveal-modal">&#215;</a>
    </div>
    <div id="editList" class="reveal-modal tiny" data-reveal>
      <h2 class="text-center">Edit List <small>(ID: <span class="editList_id_element"></span>)</small></h2>
      <form action="{{{ URL::route('list_edit') }}}" method="POST">
        <div class="row">
          <div class="large-12 columns">
            <label><strong>List Privacy</strong>
              <select name="privacy" class="editList_privacy">
                <option value="1">Public</option>
                <option value="2">Friends Only</option>
                <option value="3">Private</option>
              </select>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <label><strong>List Title</strong>
              <input class="editList_title" type="text" placeholder="Fancy Title" name="title">
            </label>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <input type="hidden" name="list_id" class="editList_id">
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
            <button type="submit" class="button expand">Save List</button>
          </div>
        </div>
      </form>
      <form action="{{{ URL::route('list_delete') }}}" method="POST">
        <div class="row">
          <div class="large-12 columns">
            <input type="hidden" name="list_id" class="editList_id">
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
            <button type="submit" class="button tiny alert expand">Delete List</button>
          </div>
        </div>
      </form>
      <a class="close-reveal-modal">&#215;</a>
    </div>
    @endif
    <div id="advSearchModal" class="reveal-modal tiny" data-reveal>
      <h2 class="text-center">Advanced Search</h2>
      <form action="{{{ URL::route('search_multi') }}}" method="POST">
        <div class="row">
          <div class="large-12 columns">
            <label>
              <select name="search_type">
                <option value="1">URL / STEAM2 ID / STEAM3 ID (64bit)</option>
                <option value="2" disabled>"Status" Paste from Developer Console</option>
              </select>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <label>
              <textarea name="search" placeholder="User Info Here (New Line per User)"></textarea>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
            <button onClick="javascript:disableFormButton(this.form);" name="<button></button>" type="submit" class="button expand">Search</button>
          </div>
        </div>
      </form>
      <a class="close-reveal-modal">&#215;</a>
    </div>

    @section('modal')
    @show

    <div class="sticky">
      <div class="contain-to-grid fixed">
        <nav class="top-bar" data-topbar>
          <ul class="title-area">
            <li class="name">
              <h1><a href="{{{ URL::route('home') }}}">VacStatus</a></h1>
            </li>
            <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
          </ul>

          <section class="top-bar-section">
            <ul class="left">
              <form action="{{{ URL::route('search_single') }}}" method="POST">
                <li class="has-form">
                  <div class="row collapse">
                    <div class="large-9 small-7 columns">
                      <input name="search" type="text" placeholder="Quick User Search">
                    </div>
                    <div class="large-2 small-3 columns">
                      <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
                      <button type="submit" class="button expand" title="Quick Search"><i class="fa fa-search"></i></button>
                    </div>
                    <div class="large-1 small-2 columns">
                      <button type="button" class="button expand" title="Advanced Search" data-reveal-id="advSearchModal"><i class="fa fa-bars"></i></button>
                    </div>
                  </div>
                </li>
              </form>
            </ul>
            <ul class="right">
              @if(Auth::check())
              @if(Auth::User()->isAdmin())
              <li>
                <a class="alert" href="{{{ URL::route('admin_home') }}}">
                  Admin CP
                </a>
              </li>
              @endif
              <li class="has-dropdown">
                <a>{{{ Auth::user()->getUserName() }}}
                @if(Auth::User()->isAdmin() && Cache::has('steamAPICalls_'.date('M_j_Y')))
               ( {{{ Cache::get('steamAPICalls_'.date('M_j_Y')) }}} )
                @endif</a>
                <ul class="dropdown">
                  <li><a href="{{{ URL::route('profile', Array('steam3Id'=> Auth::user()->getSteam3Id() )) }}}"><i class="fa fa-user"></i> Profile</a></li>
                  {{-- } <li><a href="#"><i class="fa fa-cog"></i> User CP</a></li> --}}
                  <li class="divider"></li>
                  <li><a class="alert" href="{{{ URL::route('logout') }}}"><i class="fa fa-power-off"></i> Logout</a></li>
                </ul>
              </li>
              @else
              <li>
                <a href="{{{ URL::route('login') }}}">
                  <img src="{{{ asset('img/steamlogin.png') }}}">
                </a>
              </li>
              @endif
            </ul>
          </section>
        </nav>
      </div>
    </div>

    <div class="row content-start">
    @section('content')
    @show
    </div>

    <div class="footer">
      <div class="row">
        <p class="large-7 medium-8 columns">
          © 2014 VacStatus &middot;
          <a href="https://github.com/jung3o/VacStatus" target="_blank">Github</a> &middot;
          <a href="http://jung3o.com" target="_blank">Jung Oh</a>
          [
          <a href="mailto:jung3o@yahoo.com">Email</a> &middot;
          <a href="http://steamcommunity.com/id/Jung3o/" target="_blank">Steam</a> &middot;
          <a href="http://facepunch.com/member.php?u=451226" target="_blank">Facepunch</a> &middot;
          <a href="http://www.reddit.com/user/jung3o/" target="_blank">Reddit</a>
          ]
        </p>
        <p class="large-5 medium-4 columns text-right">
          <a href="{{{ URL::route('donation') }}}">Donate</a> &middot;
          <a href="/privacy">Privacy Policy</a> &middot;
          Powered By <a href="http://steampowered.com" target="_blank">Steam</a>
        </p>
      </div>
    </div>

    <div class="loader"><span></span>... <i class="fa fa-refresh fa-spin"></i></div>
    <div class="error-notification">Something Terrible Happened!</div>
    <div class="success-notification">Something Terrible Happened!</div>
    @if(Auth::check() && Auth::user()->isAdmin() && (App::environment() == 'local' || gethostname() == 'homestead'))
    {{-- */$queries = DB::getQueryLog(); /*--}}
    {{ var_dump(count($queries), $queries) }}
    @endif

    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.3.1/js/foundation.min.js"></script>
    <script type="text/javascript" src="/js/app.js"></script>
    @section('javascript')
    @show
    <script>
      @if(Session::has('error'))
      fadInOutAlert('<strong>Error</strong> {{{ Session::get('error') }}}', 2);
      @endif

      @if(Session::has('success'))
      fadInOutSuccess('<strong>Success</strong> {{{ Session::get('success') }}}', 2);
      @endif
    </script>
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-50795838-1', 'vacstatus.com');
      ga('require', 'displayfeatures');
      ga('send', 'pageview');
    </script>
  </body>
</html>
