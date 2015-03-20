var currentPage = $('#news').data('page');

var News = React.createClass({

	fetchNews: function()
	{
		$.ajax({
			url: '/api/v1/news/?page='+currentPage,
			dataType: 'json',
			success: function(data) {
				this.setState({data: data});
			}.bind(this),
			error: function(xhr, status, err) {
				console.error(this.props.url, status, err.toString());
			}.bind(this)
		});
	},

	componentDidMount: function()
	{
		this.fetchNews();
	},

	getInitialState: function()
	{
		return {
			data: null
		};
	},
	
	render: function() {
		var data, news, pagination;

		data = this.state.data;
		
		if(data !== null)
		{
			if(data.error)
			{
				return <h1 className="text-center">{data.error}</h1>
			}

			if(data.data !== null)
			{
				news = data.data.map(function(article, index)
				{
					return (
				        <div id={ "news_" + article.id } key={ index } className="article">
				        	<h3>{ article.title }<br /><small>{ article.created_at }</small></h3>
				        	<div className="article-content" dangerouslySetInnerHTML={{__html: article.body }} />
				        	<hr className="divider" />
				        </div>
			        );
				});
			}

			if(data.next_page !== null) {

			}

			return (
				<div className="container">
					<div className="row">
						<div className="col-xs-12">
							<h1>News</h1>
							{ news }
							<nav>
								<ul className="pager">
									<li className={"previous" + (data.prev_page === null ? ' disabled' : '')}>
										<a href={ data.prev_page !== null ? "/news/"+ (data.current_page - 1) : '#'}><span>&larr;</span> Older</a>
									</li>
									<li className={"next" + (data.next_page === null ? ' disabled' : '')}>
										<a href={ data.next_page !== null ? "/news/" + (data.current_page + 1) : '#'}>Newer <span>&rarr;</span></a>
									</li>
								</ul>
							</nav>
							<br />
						</div>
					</div>
				</div>
	        );
		}

		return <div></div>;
	}
});

React.render(<News />, document.getElementById('news'));