@extends('layout.app')

@section('content')
	<div id="list" class="list-page" data-grab="{{ $grab }}" data-search="{{ isset($search) ? $search : ''}}">
		{{-- <div class="list-action-bar hidden-lg">
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<a href="#" data-toggle="collapse" data-target="#list-actions"><span class="fa fa-bars"></span>&nbsp; Advanced Options</a>
						<div id="list-actions" class="list-actions collapse">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-lg-3">
					<div class="list-actions visible-lg-block">
					</div>
				</div>
				<div class="col-xs-12 col-lg-9">
					<h2 class="list-title">
						Most Tracked Users<br>
						<small>By: TestUser</small>
					</h2>
					<div class="table-responsive">
						<table class="table list-table">
							<tr>
								<th width="48"></th>
								<th>User</th>
								<th class="text-center" width="120">VAC Ban</th>
								<th class="text-center hidden-sm" width="140">Community Ban</th>
								<th class="text-center hidden-sm" width="100">Trade Ban</th>
								<th class="text-center" width="100">Tracked By</th>
							</tr>
							<tr>
								<td class="user_avatar">
									<img src="https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/4a/4ac72ad425d8303daf5239e5b729ae42f6aab5da.jpg">
								</td>
								<td class="user_name">
									JWonderchild
								</td>
								<td class="user_vac_ban text-center">
									<span class="text-danger">Nov 21 2014</span>
								</td>
								<td class="user_community_ban text-center hidden-sm">
									<span class="fa fa-times text-danger"></span>
								</td>
								<td class="user_trade_ban text-center hidden-sm">
									<span class="fa fa-check text-success"></span>
								</td>
								<td class="user_track_number text-center">
									113
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div> --}}
	</div>
@stop

@section('js')
	<script src="/js/pages/list.js"></script>
@stop