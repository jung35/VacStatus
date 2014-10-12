@extends('admin/adminLayout')

@section('subcontent')
  <h4 class="text-center">Edit News &amp; Update</h4>

  <form class="small-12 columns" action="{{{ URL::route('admin_news_post_edit') }}}" method="POST">
    <input type="text" name="news_title" placeholder="Title" value="{{{ $news->title }}}">
    <textarea name="news_body" rows="20" placeholder="Poop">{{{ $news->body }}}</textarea>
    {{ Form::token() }}
    <input type="hidden" name="news_id" value="{{{ $news->id }}}">
    <div class="row">
      <div class="small-6 columns">
        <button type="submit" class="button expand">Save News</button>
      </div>
      <div class="small-6 columns">
        <a href="{{{ URL::route('admin_news') }}}" class="button alert expand">Cancel</a>
      </div>
    </div>
  </form>
@stop
