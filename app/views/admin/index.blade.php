@extends('admin/adminLayout')

@section('subcontent')
  <div class="large-12 columns">
    <h4>Site Info</h4>
  </div>
  <div class="large-4 medium-6 column">
    <table width="100%">
      <thead>
        <tr>
          <th>Type</th>
          <th width="90px">#</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Steam Profiles</td>
          <td>{{{ $siteInfo['admin-profiles'] }}}</td>
        </tr>
        <tr>
          <td>Users</td>
          <td>{{{ $siteInfo['admin-users'] }}}</td>
        </tr>
        <tr>
          <td>Lists Total</td>
          <td>{{{ $siteInfo['admin-list-total'] }}}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="large-4 medium-6 column">
    <table width="100%">
      <thead>
        <tr>
          <th>Type</th>
          <th width="90px">#</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Public Lists</td>
          <td>{{{ $siteInfo['admin-list-public'] }}}</td>
        </tr>
        <tr>
          <td>Friends Lists</td>
          <td>{{{ $siteInfo['admin-list-friend'] }}}</td>
        </tr>
        <tr>
          <td>Private Lists</td>
          <td>{{{ $siteInfo['admin-list-private'] }}}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="large-4 medium-6 column">
    <table width="100%">
      <thead>
        <tr>
          <th>Type</th>
          <th width="90px">#</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Donations</td>
          <td>{{{ $siteInfo['admin-donation'] }}}</td>
        </tr>
        <tr>
          <td>After Processed</td>
          <td>{{{ $siteInfo['admin-donation-after'] }}}</td>
        </tr>
        <tr>
          <td>Avg. Original</td>
          <td>{{{ $siteInfo['admin-donation-average'] }}}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="small-12 column">
    <h4>Site Logs</h4>
    <table width="100%">
      <tr>
        <th>File (Last 5 Days)</th>
        <th>Size (KB)</th>
      </tr>
      @foreach($logs as $log)
      <tr>
        <td><a href="#">{{{ $log[0] }}}</a></td>
        <td>{{{ $log[1] }}}</td>
      </tr>
      @endforeach
    </table>
  </div>
@stop
