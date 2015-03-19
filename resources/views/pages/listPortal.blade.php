@extends('layout.app')

@section('content')
	<div id="listPortal" class="listPortal-page">
{{-- 		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="special-list">
						<h3>Special Lists</h3>
						<a href="/list/most">Most Tracked Users</a>
						<a href="/list/latest">Latest Added Users</a>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="custom-list">
						<h3>My Lists</h3>
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th class="text-center" width="20px">ID</th>
										<th>Title</th>
										<th class="text-center" width="150px">Users In List</th>
										<th class="text-center" width="150px">Subscribers</th>
										<th class="text-center" width="150px">Created Date</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="text-center">1</td>
										<td>RIP in VACation</td>
										<td class="text-center">44</td>
										<td class="text-center">1</td>
										<td class="text-center">Nov 21 2014</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<div class="custom-list">
						<h3>Friend's Lists</h3>
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th class="text-center" width="32px"></th> {{-- avatar --}}
										<th width="200px">User</th>
										<th>Title</th>
										<th class="text-center" width="150px">Users In List</th>
										<th class="text-center" width="150px">Subscribers</th>
										<th class="text-center" width="150px">Created Date</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="text-center"><img src="https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/73/73a100a37d48cdbdb91b447c546522cbb9a53170.jpg"></td>
										<td>FatBoyXPC</td>
										<td>Cheaterzzz</td>
										<td class="text-center">44</td>
										<td class="text-center">1</td>
										<td class="text-center">Nov 21 2014</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div> --}}
	</div>
@stop

@section('js')
	<script src="/js/pages/listPortal.js"></script>
@stop