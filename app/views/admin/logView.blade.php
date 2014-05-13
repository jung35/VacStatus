@extends('admin.adminBase')

@section('adminContent')
<div class="col-md-12">
  <h3 class="text-center">Log : {{{ $info['name'] }}}<br><small>{{{ $info['size'] }}}KB</small></h3>
  <pre>
@foreach($log as $line)
@if(strtolower($line[1]) != 'info')
{{{ $line[0] }}} <span class="label label-{{{ strtolower($line[1]) == 'error'? 'danger': strtolower($line[1]) }}}">{{{ $line[1] }}}</span> {{{ $line[2] }}}

@endif
@endforeach
</pre>
</div>
@stop
