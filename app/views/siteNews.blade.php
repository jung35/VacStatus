@extends('base')
@section('title')
&mdash; News
@stop

@section('content')
  <h1>Site News &amp; Updates</h1>
  <div class="col-sm-8">
    @foreach($siteNewses as $siteNews)
    <div id="news-{{{ $siteNews->id }}}" class="news">
      <h3>{{{ $siteNews->title }}} <small>{{{ date('m/d/Y', strtotime($siteNews->created_at))}}}</small></h3>
      <p class="news-pfont">{{{ $siteNews->news }}}</p>
    </div>
    @endforeach
    {{ $siteNewses->links() }}
  </div>
@stop
