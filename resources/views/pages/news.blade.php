@extends('layout.app')

@section('content')
	<div id="news" class="news-page" data-page="{{ $page }}">
	</div>
@stop

@section('js')
	<script src="/js/pages/news.js"></script>
@stop