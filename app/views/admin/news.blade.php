@extends('admin/adminLayout')

@section('subcontent')
  <h4 class="text-center">News &amp; Update</h4>

  <table width="100%">
    <thead>
      <tr>
        <th>#</th>
        <th>Title</th>
        <th>Created Date</th>
        <th>Updated Date</th>
        <th width="59px"></th>
        <th width="72px"></th>
      </tr>
    </thead>
    <tbody>

    @foreach($news as $article)
      <tr>
        <td>{{{ $article->id }}}</td>
        <td>{{{ $article->title }}}</td>
        <td>{{{ date('m/d/Y', strtotime($article->created_at))}}}</td>
        <td>{{{ date('m/d/Y', strtotime($article->updated_at))}}}</td>
        <td>
          <a href="{{{ URL::route('admin_news_edit', $article->id) }}}" class="button tiny">Edit</a>
        </td>
        <td>
          <form action="{{{ URL::route('admin_news_delete') }}}" method="POST">
            <input type="hidden" name="news_id" value="{{{ $article->id }}}">
            {{ Form::token() }}
            <button type="submit" class="button tiny alert">Delete</button>
          </form>
        </td>
      </tr>
    @endforeach

    </tbody>
  </table>

  {{ $news->links() }}

  <fieldset>
    <legend>New News</legend>
    <form class="small-12 columns" action="{{{ URL::route('admin_news_create') }}}" method="POST">
      <input type="text" name="news_title" placeholder="Title">
      <textarea name="news_body" rows="10" placeholder="Poop"></textarea>
      {{ Form::token() }}
      <button type="submit" class="button expand">Create New News</button>
    </form>
  </fieldset>
@stop
