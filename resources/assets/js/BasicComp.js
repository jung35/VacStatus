'use strict';

import React from 'react';
import Notify from './Notify';

export default class BasicComp extends React.Component {

	constructor(props) {
		super(props);

		this.state = {};
		this.request = {};
		this.notify = new Notify;

		this.authCheck = $('meta[name="auth"]').attr('content');
		this._token = $('meta[name="_token"]').attr('content');

		$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': this._token } });
	}

	componentWillUnmount() {
		$.each(this.request, (k, val) => {
			if(val) val.abort();
		});
	}

	listPrivacy(privacy) {
		let type = {
			name: "Public",
			color: "success"
		};

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
		}

		return type;
	}

	userTitle(data) {
		let title = "";

		if(data.beta >= 1) title = "beta-name";
		if(data.donation >= 10.0) title = "donator-name";
		if(data.site_admin >= 1) title = "admin-name";

		return title;
	}

	render() {
		return <div>test</div>;
	}
}