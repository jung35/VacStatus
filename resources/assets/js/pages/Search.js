'use strict';

class Search extends BasicComp {
	render() {
		return <List search={ this.props.params.searchId } />;
	}
}