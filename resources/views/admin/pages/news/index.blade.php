@extends('admin.layout.app')

@section('content')
	<div class="admin-page">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<h1>News</h1>
					<div class="col-xs-12">
						<fieldset>
							<legend>New News</legend>
							<form action="{{ route('admin.news.save') }}" method="POST">
								<div class="form-group">
									<input class="form-control" type="text" name="news_title" placeholder="Title">
								</div>
								<div class="form-group">
									<textarea class="form-control" name="news_body" rows="10" placeholder="Poop"></textarea>
								</div>
								<div class="form-group">
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
									<button type="submit" class="btn btn-primary btn-block form-control">Create New News</button>
								</div>
							</form>
						</fieldset>
					</div>
					<table class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Title</th>
								<th>Created Date</th>
								<th>Updated Date</th>
								<th width="59px"></th>
								<th width="72px"></th>
							</tr>
						</thead>
						<tbody>
							@foreach($news as $article)
							<tr>
								<td>{{{ $article->id }}}</td>
								<td>{{{ $article->title }}}</td>
								<td>{{{ date('m/d/Y', strtotime($article->created_at))}}}</td>
								<td>{{{ date('m/d/Y', strtotime($article->updated_at))}}}</td>
								<td>
									<a href="{{ route('admin.news.edit', $article->id) }}" class="btn btn-info">Edit</a>
								</td>
								<td>
									<form action="{{ route('admin.news.delete', $article->id) }}" method="POST">
										<input type="hidden" name="news_id" value="{{ $article->id }}">
										<input type="hidden" name="_method" value="DELETE">
										<input type="hidden" name="_token" value="{{ csrf_token() }}">
										<button type="submit" class="btn btn-danger">Delete</button>
									</form>
								</td>
							</tr>
							@endforeach

						</tbody>
					</table>

					{{ $news->render() }}
				</div>
			</div>
		</div>
	</div>
@stop