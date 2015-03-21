@extends('admin.layout.app')

@section('content')
	<div class="admin-page">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<h1>
						<a href="{{ route('admin.db') }}">
							<span class="fa fa-arrow-left"></span>
						</a>
						Users
					</h1>
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th>id</th>
								<th>small_id</th>
								<th>display_name</th>
								<th>donation</th>
								<th>site_admin</th>
								<th>beta</th>
								<th>created_at</th>
								<th>updated_at</th>
							</tr>
							@foreach ($users as $user)
								<tr>
									<td>{{ $user->id }}</td>
									<td>{{ $user->small_id }}</td>
									<td>{{ $user->display_name }}</td>
									<td>{{ $user->donation }}</td>
									<td>{{ $user->site_admin }}</td>
									<td>{{ $user->beta }}</td>
									<td>{{ $user->created_at }}</td>
									<td>{{ $user->updated_at }}</td>
								</tr>
							@endforeach
						</table>
					</div>
					
					
					{!! $users->render() !!}
				</div>
			</div>
		</div>
	</div>
@stop