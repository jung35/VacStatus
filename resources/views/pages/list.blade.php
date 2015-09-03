@extends('layout.app')

@section('content')
	<div id="list" class="list-page" data-grab="{{ $grab }}" data-search="{{ isset($search) ? $search : ''}}"></div>
@stop