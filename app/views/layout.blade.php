<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
    <meta name="author" content="Jung Oh">
    <meta itemprop="name" content="VacStatus" />
    <meta name="description" itemprop="description" content="In a game like Counter-Strike: Global Offensive, you do not get a notification of some sort when the hacker gets banned. With VacStatus, you can now can keep in check of the possible hacker you reported and when that hacker gets banned, you can even recieve an email notification!">
    <meta itemprop=image content="http://vacstatus.com/favicon.png"/>
    <meta name="keywords" content="vac, status, vacstatus, vban, vbanstatus, vb, vs, vacstatus.com, vac.com, vban.com, vbanstatus.com, vac status, vban status, list, vac list, vac ban list, ban list, steam, cs, csgo, cs go, tf2 , tf, css, valve, hl, hl2, steam ban, steam ban list, valve anti-cheat, anti cheat, anti-cheat, valve cheat"/>
    <meta name="robots" content="All" />
    <meta name="revisit-after" content="1 DAYS" />

    <title>
      VacStatus &mdash;
      @section('title')
      Keep track of people's VAC status in a list
      @show
    </title>
    <link rel="stylesheet" href="stylesheets/app.css" />
    <script src="bower_components/modernizr/modernizr.js"></script>
  </head>
  <body>
    <div id="advSearchModal" class="reveal-modal tiny" data-reveal>
      <h2 class="text-center">Advanced Search</h2>
      <form>
        <div class="row">
          <div class="large-12 columns">
            <label>
              <select>
                <option value="1">URL / STEAM2 ID / STEAM3 ID (64bit)</option>
                <option value="2">"Status" Paste from Developer Console</option>
              </select>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <label>
              <textarea placeholder="User Info Here"></textarea>
            </label>
          </div>
        </div>
        <div class="row">
          <div class="large-12 columns">
            <button type="button" class="button expand">Search</button>
          </div>
        </div>
      </form>
      <a class="close-reveal-modal">&#215;</a>
    </div>

    <div class="sticky">
      <div class="contain-to-grid fixed">
        <nav class="top-bar" data-topbar>
          <ul class="title-area">
            <li class="name">
              <h1><a href="index.html">VacStatus</a></h1>
            </li>
            <li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
          </ul>

          <section class="top-bar-section">
            <ul class="left">
              <li class="has-form">
                <div class="row collapse">
                  <div class="large-9 small-7 columns">
                    <input type="text" placeholder="Quick User Search">
                  </div>
                  <div class="large-2 small-3 columns">
                    <a href="#" class="button expand" title="Quick Search"><i class="fa fa-search"></i></a>
                  </div>
                  <div class="large-1 small-2 columns">
                    <a href="#" class="button expand" title="Advanced Search" data-reveal-id="advSearchModal"><i class="fa fa-bars"></i></a>
                  </div>
                </div>
              </li>
            </ul>
            <ul class="right">
              <li class="has-dropdown">
                <a href="#">Jung</a>
                <ul class="dropdown">
                  <li><a href="#"><i class="fa fa-user"></i> Profile</a></li>
                  <li><a href="#"><i class="fa fa-cog"></i> User CP</a></li>
                  <li class="divider"></li>
                  <li><a class="alert" href="#"><i class="fa fa-power-off"></i> Logout</a></li>
                </ul>
              </li>
              <!-- <li><a href="#"><img src="http://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_small.png"></a></li> -->
            </ul>
          </section>
        </nav>
      </div>
    </div>

    <div class="row content-start">

    </div>

    <div class="footer">
      <div class="row">
        <p class="large-7 medium-8 columns">
          © 2014 VacStatus &middot;
          <a href="https://github.com/jung3o/VacStatus" target="_blank">Github</a> &middot;
          <a href="http://jung3o.com" target="_blank">Jung Oh</a>
          [
          <a href="http://steamcommunity.com/id/Jung3o/" target="_blank">Steam</a> &middot;
          <a href="http://facepunch.com/member.php?u=451226" target="_blank">Facepunch</a> &middot;
          <a href="http://www.reddit.com/user/jung3o/" target="_blank">Reddit</a>
          ]
        </p>
        <p class="large-5 medium-4 columns text-right">
          <a href="#">FAQ</a> &middot;
          <a href="#">Privacy Policy</a> &middot;
          Powered By <a href="http://steampowered.com" target="_blank">Steam</a>
        </p>
      </div>
    </div>

    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/foundation/js/foundation.min.js"></script>
    <script src="js/app.js"></script>
  </body>
</html>
