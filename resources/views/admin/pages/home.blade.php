@extends('admin.layout.app')

@section('content')
	<div class="admin-page">
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-md-6">
					<h3>Announcement</h3>
					<form action="{{ route('admin.announcement.save') }}" method="post">
						<div class="form-group">
							<textarea name="announcement" cols="30" rows="10" class="form-control">{!! Cache::get('announcement') !!}</textarea>
						</div>
						<div class="form-group">
						 	<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<button class="btn btn-block btn-primary form-control">Update Announcement</button>
						</div>
					</form>
				</div>
				<div class="col-xs-12 col-md-6">
					<h3>Logs</h3>
				</div>
			</div>
		</div>
	</div>
@stop