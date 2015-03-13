<div class="admin-header">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<ul>
					<li class="@setActiveLink('admin.home')">
						<a href="{{ route('admin.home') }}">Admin Home</a>
					</li>
					<li class="@setActiveLink('admin.news')">
						<a href="#">News</a>
					</li>
					<li class="@setActiveLink('admin.users')">
						<a href="{{ route('admin.users') }}">Users</a>
					</li>
					<li class="@setActiveLink('admin.profiles')">
						<a href="{{ route('admin.profiles') }}">Profiles</a>
					</li>
					<li class="@setActiveLink('admin.logs')">
						<a href="#">Logs</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>