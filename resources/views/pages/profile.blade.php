@extends('layout.app')

@section('title')
&mdash; Profile
@stop

@section('content')
	<div id="profile" class="profile-page" data-steam64bitid="{{ $steam64BitId }}"></div>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
				<div id="disqus_thread" class="disqus_thread"></div>
			</div>
		</div>
	</div>
@stop