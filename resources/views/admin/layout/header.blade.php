<div class="admin-header">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<ul>
					<li class="@setActiveLink('admin.home')">
						<a href="{{ route('admin.home') }}">Admin Home</a>
					</li>
					<li class="@setActiveLink('admin.news')">
						<a href="{{ route('admin.news') }}">News</a>
					</li>
					<li class="@setActiveLink('admin.db')">
						<a href="{{ route('admin.db') }}">Database</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>