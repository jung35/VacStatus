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

@section('js')
	<script type="text/javascript">
		var disqus_shortname = 'vbanstatus';
		var disqus_identifier = '{{ $steam64BitId }}';
		var disqus_title = 'VacStatus [{{ $steam64BitId }}]';

		/* * * DON'T EDIT BELOW THIS LINE * * */
		(function() {
			var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
			dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
			(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();
	</script>
@stop