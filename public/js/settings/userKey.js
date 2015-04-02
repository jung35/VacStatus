var UserKey = React.createClass({displayName: "UserKey",
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
           	React.createElement("form", {onSubmit: this.handleSubmit, className: "settings-form form-horizontal"}, 
				React.createElement("div", {className: "form-group"}, 
					React.createElement("label", {htmlFor: "userKeyInput", className: "col-sm-2 control-label"}, "Key"), 
					React.createElement("div", {className: "col-sm-8"}, 
						React.createElement("input", {disabled: true, type: "text", className: "form-control", id: "userKeyInput", ref: "userKeyInput", placeholder: "Press to Generate Key", value: userKeyValue })
					), 
					React.createElement("div", {className: "col-sm-2"}, 
						React.createElement("button", {className: "btn btn-block btn-primary"}, "Generate")
					)
				)
			)
        );

		return (
        	React.createElement("div", {className: "row"}, 
				React.createElement("div", {className: "col-xs-12 col-md-6"}, 
					React.createElement("h3", null, "Private Key ", React.createElement("small", null, "— Use this key to give permission to 3rd party applications")), 
					userKeyForm 
				)
        	)
        );
	}
});
React.render(React.createElement(UserKey, null), document.getElementById('userKey'));