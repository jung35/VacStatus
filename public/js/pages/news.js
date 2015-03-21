var currentPage = $('#news').data('page');

var News = React.createClass({displayName: "News",

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
				return React.createElement("h1", {className: "text-center"}, data.error)
			}

			if(data.data !== null)
			{
				news = data.data.map(function(article, index)
				{
					return (
				        React.createElement("div", {id:  "news_" + article.id, key: index, className: "article"}, 
				        	React.createElement("h3", null,  article.title, React.createElement("br", null), React.createElement("small", null,  article.created_at)), 
				        	React.createElement("div", {className: "article-content", dangerouslySetInnerHTML: {__html: article.body}}), 
				        	React.createElement("hr", {className: "divider"})
				        )
			        );
				});
			}

			if(data.next_page !== null) {

			}

			return (
				React.createElement("div", {className: "container"}, 
					React.createElement("div", {className: "row"}, 
						React.createElement("div", {className: "col-xs-12"}, 
							React.createElement("h1", null, "News"), 
							news, 
							React.createElement("nav", null, 
								React.createElement("ul", {className: "pager"}, 
									React.createElement("li", {className: "previous" + (data.prev_page === null ? ' disabled' : '')}, 
										React.createElement("a", {href:  data.prev_page !== null ? "/news/"+ (data.current_page - 1) : '#'}, React.createElement("span", null, "←"), " Older")
									), 
									React.createElement("li", {className: "next" + (data.next_page === null ? ' disabled' : '')}, 
										React.createElement("a", {href:  data.next_page !== null ? "/news/" + (data.current_page + 1) : '#'}, "Newer ", React.createElement("span", null, "→"))
									)
								)
							), 
							React.createElement("br", null)
						)
					)
				)
	        );
		}

		return React.createElement("div", null);
	}
});

React.render(React.createElement(News, null), document.getElementById('news'));