@extends('admin.adminBase')

@section('breadcrumb')
<li><a href="{{{ URL::route('admin.index') }}}">Home</a></li>
<li class="active">{{{ $info['name'] }}}</li>
@stop

@section('adminContent')
<div class="col-md-12">
  <h3 class="text-center">Log : {{{ $info['name'] }}}<br><small>{{{ $info['size'] }}}KB</small></h3>
  <div class="log-settings">Hide :
    <span id="log-setting-info" title="info" class="text-info">INFO</span>
    <span id="log-setting-warning" title="warning" class="text-warning">WARNING</span>
    <span id="log-setting-error" title="error" class="text-danger">ERROR</span>
  </div>
  <pre class="log"><!--
@foreach($log as $line)
--><div class="line line-{{{ strtolower($line[1]) }}}">{{{ $line[0] }}} {{{ $line[2] }}} <span class="caret"></span></div><!--
--><div class="debug line-{{{ strtolower($line[1]) }}}">{{{ var_dump(json_decode($line[3])) }}}</div><!-- @endforeach
--></pre>
</div>

@stop

@section('script')
  <script type="text/javascript" src="{{{ URL::route('home') }}}/js/admin/logView.js"></script>
@stop
