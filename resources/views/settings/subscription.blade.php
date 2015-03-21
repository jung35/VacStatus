@extends('layout.app')

@section('content')
	<div id="subscription" class="subscription-settings">
{{-- 		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<h1>Settings</h1>
					<div class="row">
						<div class="col-xs-12 col-md-6">
							<h3>Receive Updates <small>&mdash; You only need to enter in one of them</small></h3>
							<form class="subscribe-form form-horizontal">
								<div class="form-group">
									<label for="subcribeEmail" class="col-sm-2 control-label">Email</label>
									<div class="col-sm-10">
										<input type="email" class="form-control" id="subcribeEmail" ref="subcribeEmail" placeholder="Email">
									</div>
								</div>
								<div class="form-group">
									<label for="subcribePushBullet" class="col-sm-2 control-label">Pushbullet</label>
									<div class="col-sm-10">
										<input type="email" class="form-control" id="subcribePushBullet" ref="subcribePushBullet" placeholder="PushBullet Email">
									</div>
								</div>
								<div class="form-group">
    								<div class="col-sm-offset-2 col-sm-10">
										<button class="btn btn-block btn-primary">Add Email</button>
									</div>
								</div>
							</form>			
						</div>
						<div class="col-xs-12 col-md-6">
							<h3>Subscribed Lists <small>&mdash; You need to subscribe a list to receive notification</small></h3>
							<div class="subscribed-list">
								<div class="row">
									<div class="col-xs-6 col-sm-4">
										<a href="#">
											<div class="panel panel-default">
												<div class="panel-body">
													<div class="list-name">RIP in VACaction</div>
													<div class="list-author">Jung</div>
												</div>
											</div>
										</a>
									</div>
									<div class="col-xs-6 col-sm-4">
										<a href="#">
											<div class="panel panel-default">
												<div class="panel-body">
													<div class="list-name">RIP in VACaction</div>
													<div class="list-author">Jung</div>
												</div>
											</div>
										</a>
									</div>
									<div class="col-xs-6 col-sm-4">
										<a href="#">
											<div class="panel panel-default">
												<div class="panel-body">
													<div class="list-name">RIP in VACaction</div>
													<div class="list-author">Jung</div>
												</div>
											</div>
										</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> --}}
	</div>
@stop

@section('js')
	<script src="/js/settings/subscription.js"></script>
@stop