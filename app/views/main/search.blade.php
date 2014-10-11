@extends('layout')

@section('title')
Search
@stop

@section('content')
  <div class="row index-wrapper" data-equalizer>

    <div class="large-12 columns vacstatus-multilist list-display-wrapper" data-equalizer-watch>
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
  </div>

@stop
