@extends('admin.layout.app')

@section('content')
	<div class="admin-page">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<h1>Profiles</h1>
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th>id</th>
								<th>small_id</th>
								<th>display_name</th>
								<th>privacy</th>
								<th>profile_created</th>
								<th>created_at</th>
								<th>updated_at</th>
							</tr>
							@foreach ($profiles as $profile)
								<tr>
									<td>{{ $profile->id }}</td>
									<td>{{ $profile->small_id }}</td>
									<td>{{ $profile->display_name }}</td>
									<td>{{ $profile->privacy }}</td>
									<td>{{ $profile->profile_created }}</td>
									<td>{{ $profile->created_at }}</td>
									<td>{{ $profile->updated_at }}</td>
								</tr>
							@endforeach
						</table>
					</div>
					
					
					{!! $profiles->render() !!}
				</div>
			</div>
		</div>
	</div>
@stop