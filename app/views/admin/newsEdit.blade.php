@extends('admin.adminBase')

@section('breadcrumb')
<li><a href="{{{ URL::route('admin.news') }}}">News</a></li>
<li class="active">{{{ $siteNews->id }}}</li>
@stop

@section('adminContent')
<div class="col-md-12">
  <h2 class="text-center">Edit News : #{{{ $siteNews->id }}}</h2>

  {{ Form::open(array('route' => 'admin.news.edit', 'class' => 'col-md-12')) }}
    {{ Form::hidden('form-id', $siteNews->id) }}
    {{ Form::text('form-title', $siteNews->title, array('class' => 'form-control input-lg', 'placeholder' => 'Title' )) }}
    <br>
    {{ Form::textArea('form-news', $siteNews->news, array('class' => 'form-control' )) }}
    <br>
    <input type="submit" value="Save News" class="btn btn-default btn-lg btn-block">
  {{ Form::close() }}

</div>

@stop

@section('script')
  <script type="text/javascript" src="{{{ URL::route('home') }}}/js/admin/CKEditor/ckeditor.js"></script>
  <script>CKEDITOR.replace( 'form-news');</script>
@stop
