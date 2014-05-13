@extends('admin.adminBase')

@section('adminContent')
<div class="col-md-4">
  <h3 class="text-center">Site Info</h3>
  <table class="table table-bordered admin-table">
    <tr>
      <th>Type</th>
      <th>#</th>
    </tr>
    <tr>
      <td>Steam Users</td>
      <td>{{{ $stats['steamUsers'] }}}</td>
    </tr>
    <tr>
      <td>Subscribed Users</td>
      <td>{{{ $stats['subbedUsers'] }}}</td>
    </tr>
    <tr>
      <td>Recorded Users</td>
      <td>{{{ $stats['recordedUsers'] }}}</td>
    </tr>
    <tr>
      <td>Listed Users</td>
      <td>{{{ $stats['listedUsers'] }}}</td>
    </tr>
    <tr>
      <td>Avg. Users Per List</td>
      <td>{{{ $stats['avgListedUsers'] }}}</td>
    </tr>
    <tr>
      <td># Of Site News</td>
      <td>{{{ $stats['news'] }}}</td>
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
    @foreach($logs as $log)
    <tr>
      <td><a href="{{{ URL::route('admin.log', $log[0]) }}}">{{{ $log[0] }}}</a></td>
      <td>{{{ $log[1] }}}</td>
    </tr>
    @endforeach
  </table>
</div>
<div class="col-md-4">
  <h3 class="text-center">Online Users</h3>
</div>
@stop
