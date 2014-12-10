@extends('layout')

@section('title')
Settings
@stop

@section('content')

    <h1>Settings</h1>
    <hr>
    <div class="medium-2 column">
        <ul class="side-nav" role="navigation" title="Link List">
            <li role="menuitem"><a href="{{{ URL::action('SettingsController@showSettings') }}}">Add Email</a></li>
            <li role="menuitem"><a href="{{{ URL::action('SubscriptionController@index') }}}">Subscription</a></li>
        </ul>
    </div>
    <div class="medium-10 column">
        @section('settings_content')
        @show
    </div>

@stop
