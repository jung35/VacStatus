@extends('layout')

@section('title')
Search
@stop

@section('modal')
  @if(Auth::check())
  <div id="addMultipleUser" class="reveal-modal tiny" data-reveal>
    <h2 class="text-center">Add Users to List</h2>
    <form class="disable-enter" action="{{{ URL::route('list_users_add') }}}" method="POST">
      <div class="row">
        <div class="large-12 columns">
          <label><strong>Add Users into:</strong>
            <select name="list_id">
              @foreach(Auth::User()->UserList()->orderBy('id', 'DESC')->get() as $UserList)
              <option value="{{{ $UserList->getId() }}}">{{{ $UserList->getTitle() }}}</option>
              @endforeach
            </select>
          </label>
          <label><strong>Description</strong>
            <textarea name="profile_description" rows="5" placeholder="A description"></textarea>
          </label>
        </div>
      </div>
      <div class="row">
        <div class="large-12 columns">
          <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
          <input type="hidden" name="profile_ids" value="@foreach($userList as $UserListProfile)
{{ is_object($UserListProfile) ? $UserListProfile->id.',':'' }}@endforeach">
          <button type="button" name="submit" onClick="javascript:doAddMultipleUserList(this.form);" class="button expand">Add All To List</button>
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
  @endif
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
          @if(Auth::check())
            <button type="button" class="button small pull-right" onClick="javascript:MultipleAddModal();">Add all to a list</button>
          @endif
          @include('list/listTable', array('userList' => $userList))
        </div>
      </div>
    </div>
  </div>

@stop
