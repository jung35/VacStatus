'use strict';

import React from 'react';
import BasicComp from '../BasicComp';
import List from './List';

export default class Search extends BasicComp {
	render() {
		return <List search={ this.props.params.searchId } />;
	}
}