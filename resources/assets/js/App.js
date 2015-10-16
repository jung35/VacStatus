'use strict';

import React from 'react';
import ListHandler from './ListHandler';
import autobind from 'autobind-decorator';
import { Header, Footer } from './Partials';

export default class App extends React.Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		return (
			<div>
				<div className="wrap">
					<Header />
					{ React.cloneElement(this.props.children, { parentState: this.state, updateCurrentList: this.markCurrentList }) }

					<div className="pushFooter" />
					<ListHandler currentList={ this.state.listInfo } updatedCurrentList={ this.updateCurrentList } UpdateMyList={ this.updateMyList } />
				</div>

				<Footer />
			</div>
		);
	}

	@autobind
	updateMyList(myList) {
		this.setState($.extend({}, this.state, { my_list: myList }));
	}

	@autobind
	markCurrentList(listInfo) {
		this.setState($.extend({}, this.state, { listInfo: listInfo }));
	}

	@autobind
	updateCurrentList(currentList) {
		this.state.listInfo = $.extend({}, this.state.listInfo, currentList)
		this.setState(this.state);
	}
}