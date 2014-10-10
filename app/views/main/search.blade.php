@extends('layout')

@section('content')
  <div class="row index-wrapper" data-equalizer>

    <div class="large-8 medium-8 columns vacstatus-multilist list-display-wrapper" data-equalizer-watch>
      <div class="tabs-content">
        <div class="list-display content active">
          @if(!empty($invalidProfile))
          <div data-alert class="alert-box secondary">
            Couldn't find profiles for: {{{ $invalidProfile }}}
            <a href="#" class="close">&times;</a>
          </div>
          @endif
          @include('list/listTable', array('userList' => $userList))
        </div>
      </div>
    </div>

    <div class="large-4 medium-4 columns vacstatus-news" data-equalizer-watch>
      <h5>News &amp; Updates</h5>
      <ul>
        <li><span>MM/DD/YYYY&nbsp;&mdash;</span>&nbsp;<a href="#">HIHIHIHI</a></li>
      </ul>
    </div>
  </div>

@stop
