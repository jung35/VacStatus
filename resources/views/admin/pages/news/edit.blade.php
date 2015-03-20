@extends('admin.layout.app')

@section('content')
	<div class="admin-page">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<h1>Edit News</h1>

					<form action="{{ route('admin.news.save', $news->id) }}" method="POST">
						<div class="form-group">
							<input type="text" class="form-control" name="news_title" placeholder="Title" value="{{{ $news->title }}}">
						</div>
						<div class="form-group">
							<textarea name="news_body" class="form-control" rows="20" placeholder="Poop">{{{ $news->body }}}</textarea>
						</div>

						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="news_id" value="{{{ $news->id }}}">

						<div class="row">
							<div class="col-xs-6">
								<a href="{{ route('admin.news') }}" class="btn btn-default btn-block form-control">Cancel</a>
							</div>
							<div class="col-xs-6">
								<button type="submit" class="btn btn-primary btn-block form-control">Save News</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@stop