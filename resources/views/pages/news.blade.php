@extends('layout.app')

@section('content')
	<div id="news" class="news-page" data-page="{{ $page }}">
	</div>

	<div id="listHandler"></div>
@stop

@section('js')
	<script src="/js/pages/news.js"></script>
@stop