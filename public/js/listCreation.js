var ListCreation = React.createClass({displayName: "ListCreation",
	render: function()
	{
		return (
	        React.createElement("div", {className: "modal fade", id: "createListModal", tabindex: "-1", role: "dialog", "aria-labelledby": "myModalLabel", "aria-hidden": "true"}, 
				React.createElement("div", {className: "modal-dialog"}, 
					React.createElement("div", {className: "modal-content"}, 
						React.createElement("div", {className: "modal-header"}, 
							React.createElement("button", {type: "button", className: "close", "data-dismiss": "modal", "aria-label": "Close"}, React.createElement("span", {"aria-hidden": "true"}, "×")), 
							React.createElement("h4", {className: "modal-title"}, "Create New List")
						), 
						React.createElement("div", {className: "modal-body"}, 
							"..."
						), 
						React.createElement("div", {className: "modal-footer"}, 
							React.createElement("button", {type: "button", className: "btn btn-default", "data-dismiss": "modal"}, "Close"), 
							React.createElement("button", {type: "button", className: "btn btn-primary"}, "Save changes")
						)
					)
				)
			)
        );
	}
});

React.render(React.createElement(ListCreation, null), document.getElementById('createList'));