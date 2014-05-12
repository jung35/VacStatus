@extends('base')

@section('title')
&mdash; Admin CP
@stop

@section('head')
<link rel="stylesheet" href="{{{ URL::route('home') }}}/css/admin/index.css">
@stop

@section('content')
<div class="row">
  <ol class="breadcrumb">
    <li><a>Admin Panel</a></li>
    <li class="active">Home</li>
  </ol>
</div>
<div class="row">
  <ul class="col-md-2 nav nav-pills nav-stacked">
    <li class="active"><a href="{{{ URL::route('admin.index') }}}">Home</a></li>
    <li><a href="#">Profile</a></li>
    <li><a href="#">Messages</a></li>
  </ul>
  <div class="col-md-10">
    <div class="row">
      <div class="col-md-4">
        <h3 class="text-center">Site Info</h3>
        <table class="table table-bordered admin-table">
          <tr>
            <th>Type</th>
            <th>#</th>
          </tr>
          <tr>
            <td>Steam Users</td>
            <td>123</td>
          </tr>
          <tr>
            <td>Subscribed Users</td>
            <td>123</td>
          </tr>
          <tr>
            <td>Recorded Users</td>
            <td>123</td>
          </tr>
          <tr>
            <td>Listed Users</td>
            <td>123</td>
          </tr>
          <tr>
            <td>Avg. Users Per List</td>
            <td>123</td>
          </tr>
          <tr>
            <td># Of Aliases Users</td>
            <td>123</td>
          </tr>
          <tr>
            <td># Of Site News</td>
            <td>123</td>
          </tr>
        </table>
      </div>
      <div class="col-md-4">
        <h3 class="text-center">Logs</h3>
        <table class="table table-bordered admin-table">
          <tr>
            <th>File (Last 5 Days)</th>
            <th>Size (KB)</th>
          </tr>
          <tr>
            <td><a href="#">laravel-2014-05-05.log</a></td>
            <td>2</td>
          </tr>
          <tr>
            <td><a href="#">laravel-2014-05-06.log</a></td>
            <td>2</td>
          </tr>
          <tr>
            <td><a href="#">laravel-2014-05-07.log</a></td>
            <td>2</td>
          </tr>
        </table>
      </div>
      <div class="col-md-4">
        <h3 class="text-center">Online Users</h3>
      </div>
    </div>
  </div>
</div>
@stop
