@extends('admin.layout.app')

@section('content')
	<div class="admin-page">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<h1>Databases</h1>
					<div class="row db-list">
						<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
							<a href="{{ route('admin.db.users') }}">
								<div class="panel panel-default">
									<div class="panel-body">
										Users
									</div>
								</div>
							</a>
						</div>
						<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
							<a href="{{ route('admin.db.profiles') }}">
								<div class="panel panel-default">
									<div class="panel-body">
										Profiles
									</div>
								</div>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@stop