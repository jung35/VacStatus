'use strict';

class News extends BasicComp {
	componentDidMount()
	{
		let page = this.props.params.page;
		if(page == undefined || page < 1) page = 1;

		this.fetchNews(page);
	}

	fetchNews(page)
	{
		this.request.fetchNews = $.ajax({
			url: '/api/v1/news/?page='+page,
			dataType: 'json',
			success: (data) => {
				if(data.error)
				{
					this.notify.danger(data.error).run();
					return;
				}

				this.setState(data);
			},
			complete: () => {
				delete this.request.fetchNews;
			}
		});
	}

	render()
	{
		var state, news, pagination;

		state = this.state;
		
		if(state && state.current_page)
		{
			news = state.data.map(function(article, index)
			{
				return (
					<div id={ "news_" + article.id } key={ index } className="article">
						<h3>{ article.title }<br /><small>{ article.created_at }</small></h3>
						<div className="article-content" dangerouslySetInnerHTML={{__html: article.body }} />
						<hr className="divider" />
					</div>
				);
			});

			pagination = (
				<nav>
					<ul className="pager">
						<li className={"previous" + (state.prev_page_url === null ? ' disabled' : '')}>
							<a href={ state.prev_page_url != null ? "/news/"+ (state.current_page - 1) : '#'}><span>&larr;</span> Older</a>
						</li>
						<li className={"next" + (state.next_page_url === null ? ' disabled' : '')}>
							<a href={ state.next_page_url != null ? "/news/" + (state.current_page + 1) : '#'}>Newer <span>&rarr;</span></a>
						</li>
					</ul>
				</nav>
			);
		}

		return (
			<div className="container">
				<div className="row">
					<div className="col-xs-12">
						<h1>News</h1>
						{ news }{ pagination }
						<br />
					</div>
				</div>
			</div>
		);
	}
}
