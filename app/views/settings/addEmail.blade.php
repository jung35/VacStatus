@extends('settings/layout')

@section('settings_content')
  <h3>Add Email<br><small>This is so that you can recieve notifications on the lists that you have subscribed to when a person is banned</small></h3>
  {{ Form::open(array('action' => 'SettingsController@editSettings')) }}
    <div class="row">
      <div class="small-12 columns">
        <div class="row">
          <div class="medium-2 small-12 columns">
            <label for="emailLabel" class="right inline small-only-text-left">Email</label>
          </div>
          <div class="medium-10 small-12 columns {{{ $emailStatus ? 'success' : 'error' }}}">
            <input name="email" type="email" id="emailLabel">
            <small class="{{{ $emailStatus ? 'success' : 'error' }}}"> {{{ $emailStatus ? 'Subscribed at: '.$email : 'Not currently subscribed' }}}</small>
          </div>
        </div>
        <div class="row">
          <div class="medium-10 small-12 medium-offset-2 columns">
            <button class="button expand">Send Verification!</button>
          </div>
        </div>
      </div>
    </div>
  {{ Form::close() }}

@stop
