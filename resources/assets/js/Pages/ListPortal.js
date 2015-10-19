'use strict';

import React from 'react';
import { Link } from 'react-router';
import BasicComp from '../BasicComp';

export default class ListPortal extends BasicComp {
	constructor(props) {
		super(props);
		this.state = { my_list: [], friends_list: [] };
	}

	componentDidMount() {
		this.fetchLists();
	}

	fetchLists() {
		this.request.fetchLists = $.ajax({
			url: '/api/v1/list',
			dataType: 'json',
			success: (data) => {
				this.setState($.extend({}, this.state, data));
			},
			complete: () => {
				delete this.request.fetchLists;
			}
		});
	}

	componentWillReceiveProps(props) {
		let updatedState = $.extend({}, this.state, {my_list: props.parentState.my_list});

		this.setState(updatedState);
	}

	renderMyList(data) {
		if(data.length < 1) return <div className="custom-list"></div>;

		let myList = data.map((list, index) =>
		{
			let privacy = this.listPrivacy(list.privacy);

			return (
				<tr key={index}>
					<td className="text-center">{ list.id }</td>
					<td><Link className="list_link" to={"/list/" + list.id}>{ list.title }</Link></td>
					<td className={"text-center text-" + privacy.color}>{ privacy.name }</td>
					<td className="text-center">{ list.users_in_list }</td>
					<td className="text-center">{ list.sub_count }</td>
					<td className="text-center">{ list.created_at }</td>
				</tr>
			);
		});

		return (
			<div className="custom-list">
				<h3>My Lists</h3>
				<div className="table-responsive">
					<table className="table">
						<thead>
							<tr>
								<th className="text-center" width="25px">ID</th>
								<th>List Name</th>
								<th className="text-center" width="120px">List Type</th>
								<th className="text-center" width="120px">Users In List</th>
								<th className="text-center" width="120px">Subscribers</th>
								<th className="text-center" width="120px">List Creation</th>
							</tr>
						</thead>
						<tbody>
							{ myList }
						</tbody>
					</table>
				</div>
			</div>
		);
	}

	renderFriendsList(data) {
		if(data.length < 1) return <div className="custom-list"></div>;

		let friendsList = data.map((list, index) =>
		{
			let privacy = this.listPrivacy(list.privacy);
			let userTitle = this.userTitle(list);

			return (
				<tr key={index}>
					<td className="text-center">
						<img src={ list.avatar_thumb } />
					</td>
					<td><Link className="list_link" to={"/list/" + list.user_list_id}>{ list.title }</Link></td>
					<td className={ userTitle }>{ list.display_name }</td>
					<td className={"text-center text-" + privacy.color}>{ privacy.name }</td>
					<td className="text-center">{ list.users_in_list }</td>
					<td className="text-center">{ list.sub_count }</td>
					<td className="text-center">{ list.created_at }</td>
				</tr>
			);
		});

		return (
			<div className="custom-list">
				<h3>Friends&#39; Lists</h3>
				<div className="table-responsive">
					<table className="table">
						<thead>
							<tr>
								<th className="text-center" width="32px"></th>
								<th>List Name</th>
								<th width="200px">User</th>
								<th className="text-center" width="120px">List Type</th>
								<th className="text-center" width="120px">Users In List</th>
								<th className="text-center" width="120px">Subscribers</th>
								<th className="text-center" width="120px">List Creation</th>
							</tr>
						</thead>
						<tbody>
							{ friendsList }
						</tbody>
					</table>
				</div>
			</div>
		);
	}
	
	render() {
		let myList, friendsList;

		myList = this.renderMyList(this.state.my_list);
		friendsList = this.renderFriendsList(this.state.friends_list);

		return (
			<div id="listPortal" className="listPortal-page">
				<div className="container">
					<div className="row">
						<div className="col-xs-12">
							<div className="special-list">
								<Link to="/list/most">Most Tracked Users</Link>
								<Link to="/list/latest">Latest Added Users</Link>
								<Link to="/list/latest/vac">Latest VAC Bans</Link>
								<Link to="/list/latest/game">Latest Game Bans</Link>
							</div>
						</div>
					</div>
					<div className="row">
						<div className="col-xs-12">{ myList }</div>
					</div>
					<div className="row">
						<div className="col-xs-12">{ friendsList }</div>
					</div>
				</div>
			</div>
     	);
	}
}