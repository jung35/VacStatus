<div class="footer">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-6 col-md-push-6">
				<ul class="left-footer">
					<li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
					<li><a href="{{ route('contact') }}">Contact</a></li>
					<li><a target="_blank" href="https://github.com/jung3o/VacStatus">Github</a></li>
					<li><a target="_blank" href="https://trello.com/b/xjhgGSJR/vacstatus">Trello</a></li>
				</ul>
			</div>

			<div class="col-xs-12 col-md-6 col-md-pull-6">
				<div class="right-footer copyright">
					<div>&copy; 2015 VacStatus.</div>
					<div>Powered By <a href="http://steampowered.com" target="_blank">Steam</a></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container">
	<div class="notification">
{{-- 		<div class="alert alert-success">There was an error trying to communicate with Steam Server</div>
		<div class="alert alert-danger">There was an error trying to communicate with Steam Server</div>
		<div class="alert alert-warning">There was an error trying to communicate with Steam Server</div>
		<div class="alert alert-info">There was an error trying to communicate with Steam Server</div> --}}
	</div>
</div>

<div class="modal fade" id="searchModal" tabIndex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
				<h4 class="modal-title">Look Up Users</h4>
			</div>
			<form action="{{ route('search') }}" method="post">
				<div class="modal-body">
					<div class="form-group">
						<textarea
							name="search"
							cols="30"
							rows="10"
							class="form-control"
							placeholder="2 ways to search:
 - type in steam URL/id/profile and split them in spaces or newlines or both
 - Type 'status' on console and paste the output here"
						></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Search</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/react/0.12.2/react.js"></script>
<script>
	$.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') }
    });
	var auth_check = $('meta[name=auth]').attr("content");
</script>
<script src="/js/notification.js"></script>
<script src="/js/listHandler.js"></script>