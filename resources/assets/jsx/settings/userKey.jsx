var UserKey = React.createClass({
	fetchUserKey: function()
	{
		$.ajax({
			url: '/api/v1/settings/userkey',
			dataType: 'json',
			success: function(data) {
				this.setState({data: data});
			}.bind(this),
			error: function(xhr, status, err) {
				console.error(this.props.url, status, err.toString());
			}.bind(this)
		});
	},

	handleSubmit: function(e)
	{
		e.preventDefault();

		$.ajax({
			url: '/api/v1/settings/userkey',
			type: 'POST',
			data: {
				_token: _token,
			},
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
		this.fetchUserKey();
	},

	getInitialState: function()
	{
		return {
			data: null
		};
	},
	
	render: function()
	{
		var data, userKeyForm, userKeyValue, buttonText;

		data = this.state.data;


		if(data == null)
		{
			userKeyValue = "";
		} else {
			userKeyValue = data[0];
		}

		userKeyForm = (
           	<form onSubmit={this.handleSubmit} className="settings-form form-horizontal">
				<div className="form-group">
					<label htmlFor="userKeyInput" className="col-sm-2 control-label">Key</label>
					<div className="col-sm-8">
						<input disabled type="text" className="form-control" id="userKeyInput" ref="userKeyInput" placeholder="Press to Generate Key" value={ userKeyValue } />
					</div>
					<div className="col-sm-2">
						<button className="btn btn-block btn-primary">Generate</button>
					</div>
				</div>
			</form>
        );

		return (
        	<div className="row">
				<div className="col-xs-12 col-md-6">
					<h3>Private Key <small>&mdash; Use this key to give permission to 3rd party applications</small></h3>
					{ userKeyForm }
				</div>
        	</div>
        );
	}
});
React.render(<UserKey />, document.getElementById('userKey'));