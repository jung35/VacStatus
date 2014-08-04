@extends('layout')
@section('content')

  <div class="large-12 columns">
    <h2 class="text-center">Welcome to VacStatus<small>Keep track of people's VAC status in a list</small></h2>
  @if(!Auth::check())
    <div data-alert class="alert-box vacstatus-alert-box">
      <p>In a game like Counter-Strike: Global Offensive, you do not get a notification of some sort when the hacker gets banned. With VacStatus, you can now can keep in check of the possible hacker you reported and when that hacker gets banned, you can even recieve an email notification!</p>
      <div class="row text-center vacstatus-about-box" data-equalizer>
        <div class="large-3 medium-3 columns" data-equalizer-watch>
          <h2><i class="fa fa-heart"></i></h2>
          <p><b>Free &amp; Open Source</b><br>Yup. It's free and anyone can look at the code that is making this website run!</p>
        </div>
        <div class="large-3 medium-3 columns" data-equalizer-watch>
          <h2><i class="fa fa-unlock"></i></h2>
          <p><b>No Registration Required</b><br>Registration isn't required to use the website, but it is recommended.</p>
        </div>
        <div class="large-3 medium-3 columns" data-equalizer-watch>
          <h2><i class="fa fa-steam"></i></h2>
          <p><b>Steam Integrated</b><br>Login with steam to create &amp; manage your list! You can also view your friend's list too!</p>
        </div>
        <div class="large-3 medium-3 columns" data-equalizer-watch>
          <h2><i class="fa fa-user"></i></h2>
          <p><b>Detailed Profiles</b><br>We provide detailed information about any users you lookup.</p>
        </div>
      </div>
      <div class="row text-center vacstatus-about-box" data-equalizer>
        <div class="large-3 medium-3 columns" data-equalizer-watch>
          <h2><i class="fa fa-share-alt"></i></h2>
          <p><b>Create &amp; Share Your List</b><br>Create a list to keep record of the hackers you meet &amp; share them with your friends!</p>
        </div>
        <div class="large-3 medium-3 columns" data-equalizer-watch>
          <h2><i class="fa fa-list"></i></h2>
          <p><b>Create Multiple Lists</b><br>One list isn't enough? Well, you can create up to # lists!</p>
        </div>
        <div class="large-3 medium-3 columns" data-equalizer-watch>
          <h2><i class="fa fa-users"></i></h2>
          <p><b>Comment System</b><br>Found a hacker or a scammer? Leave a comment to warn others!</p>
        </div>
        <div class="large-3 medium-3 columns" data-equalizer-watch>
          <h2><i class="fa fa-envelope"></i></h2>
          <p><b>Get Notified</b><br>Recieve an email notice whenever a hacker has been banned!</p>
        </div>
      </div>
      <!-- <a href="#" class="close">&times;</a> -->
    </div>
  @else
  <div style="height: 10px"></div>
  @endif
  </div>


  <div class="large-8 medium-8 columns vacstatus-multilist">
    <ul class="tabs" data-tab>
      <li class="tab-title active"><a href="#panel-1">Most Tracked</a></li>
      <li class="tab-title"><a href="#panel-2">Latest Added</a></li>
      @if(Auth::check())
      <li class="tab-title">
        <a data-dropdown="personalList">Personal List <i class="fa fa-caret-down"></i></a>
        <ul id="personalList" class="f-dropdown" data-dropdown-content>
          <li><a href="#">This is a link</a></li>
          <li><a href="#">This is another</a></li>
          <li><a href="#">Yet another</a></li>
          <li class="divider"></li>
          <li><a href="#">New List</a></li>
        </ul>
      </li>
      <li class="tab-title">
        <a data-dropdown="friendsList">Friends' List <i class="fa fa-caret-down"></i></a>
        <ul id="friendsList" class="f-dropdown" data-dropdown-content>
          <li class="has-dropdown" ><a href="#">Share</a>
              <ul class="dropdown" >
                  <li class="right"><a href="#">This is a link</a></li>
                  <li class="right"><a href="#">This is another</a></li>
                  <li class="right"><a href="#">Yet another</a></li>
              </ul>
          </li>
          <li class="has-dropdown" ><a href="#">Share</a>
              <ul class="dropdown" >
                  <li class="right"><a href="#">This is a link</a></li>
                  <li class="right"><a href="#">This is another</a></li>
                  <li class="right"><a href="#">Yet another</a></li>
              </ul>
          </li>
          <li class="has-dropdown" ><a href="#">Share</a>
              <ul class="dropdown" >
                  <li class="right"><a href="#">This is a link</a></li>
                  <li class="right"><a href="#">This is another</a></li>
                  <li class="right"><a href="#">Yet another</a></li>
              </ul>
          </li>
          <li class="has-dropdown" ><a href="#">Share</a>
              <ul class="dropdown" >
                  <li class="right"><a href="#">This is a link</a></li>
                  <li class="right"><a href="#">This is another</a></li>
                  <li class="right"><a href="#">Yet another</a></li>
              </ul>
          </li>
          <li class="has-dropdown" ><a href="#">Share</a>
              <ul class="dropdown" >
                  <li class="right"><a href="#">This is a link</a></li>
                  <li class="right"><a href="#">This is another</a></li>
                  <li class="right"><a href="#">Yet another</a></li>
              </ul>
          </li>
          <li class="has-dropdown" ><a href="#">Share</a>
              <ul class="dropdown" >
                  <li class="right"><a href="#">This is a link</a></li>
                  <li class="right"><a href="#">This is another</a></li>
                  <li class="right"><a href="#">Yet another</a></li>
              </ul>
          </li>
        </ul>
      </li>
      @endif
    </ul>
    <div class="tabs-content">
      <div class="content active" id="panel-1">
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
            <tr>
              <td class="vacstatus-list-avatar">
                <img src="http://media.steampowered.com/steamcommunity/public/images/avatars/0b/0b4c44093b7d018f5aba3ee18ab79b78d5baf7b4.jpg">
              </td>
              <td class="vacstatus-list-user">Tw33k</td>
              <td class="vacstatus-list-status text-alert">
                <span class="fa fa-check"></span>&nbsp;&nbsp;03/19/2014
              </td>
              <td class="vacstatus-list-tracker">21</td>
              <td class="vacstatus-list-button">
                <a data-dropdown="edit1" class="button tiny"><i class="fa fa-caret-down"></i></a>
                <ul id="edit1" class="f-dropdown" data-dropdown-content>
                  <li class="has-dropdown" ><a href="#"><i class="fa fa-plus"></i> Add</a></a>
                      <ul class="dropdown" >
                          <li class="right"><a href="#">This is a link</a></li>
                          <li class="right"><a href="#">This is another</a></li>
                          <li class="right"><a href="#">Yet another</a></li>
                      </ul>
                  </li>
                  <li><a href="#"><i class="fa fa-info"></i> Info</a></li>
                </ul>
              </td>
            </tr>

            <tr>
              <td class="vacstatus-list-avatar">
                <img src="http://media.steampowered.com/steamcommunity/public/images/avatars/0b/0b4c44093b7d018f5aba3ee18ab79b78d5baf7b4.jpg">
              </td>
              <td class="vacstatus-list-user">Tw33k</td>
              <td class="vacstatus-list-status text-success">
                <span class="fa fa-times"></span>
              </td>
              <td class="vacstatus-list-tracker">21</td>
              <td class="vacstatus-list-button">
                <a data-dropdown="edit2" class="button tiny"><i class="fa fa-caret-down"></i></a>
                <ul id="edit2" class="f-dropdown" data-dropdown-content>
                  <li class="has-dropdown" ><a href="#"><i class="fa fa-plus"></i> Add</a></a>
                      <ul class="dropdown" >
                          <li class="right"><a href="#">This is a link</a></li>
                          <li class="right"><a href="#">This is another</a></li>
                          <li class="right"><a href="#">Yet another</a></li>
                      </ul>
                  </li>
                  <li><a href="#"><i class="fa fa-info"></i> Info</a></li>
                </ul>
              </td>
            </tr>

          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="large-4 medium-4 columns vacstatus-news">
    <div class="panel">
      <h5>News &amp; Updates</h5>
      <ul>
        <li> <span>05/26/2014&nbsp;&mdash;</span>&nbsp;<a href="#">Less requests to the database</a></li>
        <li> <span>05/21/2014&nbsp;&mdash;</span>&nbsp;<a href="#">Ajax Update</a></li>
        <li> <span>05/03/2014&nbsp;&mdash;</span>&nbsp;<a href="#">Mail Notification added!</a></li>
      </ul>
    </div>
  </div>

@stop
