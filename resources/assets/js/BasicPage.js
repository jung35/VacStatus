'use strict';

class BasicPage extends React.Component {

	constructor(props) {
		super(props);

		this.state = {};
		this.request = {};
		this.notify = new Notify;
	}

	componentWillUnmount() {
		$.each(this.request, (k, val) => {
			if(val) val.abort();
		});
	}

	listPrivacy(privacy) {
		var type = {};
		switch(privacy)
		{
			case "3":
			case 3:
				type.name = "Private";
				type.color = "danger";
				break;
			case "2":
			case 2:
				type.name = "Friends Only";
				type.color = "warning";
				break;
			default:
				type.name = "Public";
				type.color = "success";
				break;
		}

		return type;
	}

	userTitle(data) {
		var title;
		if(data.beta >= 1) title = "beta-name";
		if(data.donation >= 10.0) title = "donator-name";
		if(data.site_admin >= 1) title = "admin-name";

		return title;
	}
}