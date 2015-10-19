'use strict';

import React from 'react';
import autobind from 'autobind-decorator';
import BasicComp from '../BasicComp';

export default class List extends BasicComp {
	constructor(props) {
		super(props);

		if(this.listId == undefined && this.props.search == undefined) {
			this.notify.error('Invalid List').run();
		}

		this.state = { list_info: {}, profiles: [], page: 0 };
		this.filterName = ['All', 'Ban', 'No Ban'];

		this.updateListTitle = this.updateListTitle.bind(this);
	}

	componentDidMount() {
		this.fetchList();
	}

	componentWillUnmount() {
		super.componentWillUnmount();

		if(this.props.updateCurrentList !== undefined)
		{
			this.props.updateCurrentList({});
		}
	}

	fetchList() {
		let url;

		if(this.listId != undefined) url = '/api/v1/list/'+this.listId;
		if(this.props.search != undefined) url = '/api/v1/search/'+this.props.search

		if(url == undefined) return;

		this.request.fetchList = $.ajax({
			url: url,
			dataType: 'json',
			success: (data) => {
				this.setState($.extend({}, this.state, data));
				this.profiles = data.profiles;
				
				if(this.props.updateCurrentList !== undefined)
				{
					this.props.updateCurrentList(data.list_info);
				}
			},
			complete: () => {
				delete this.request.fetchList;
			}
		});
	}

	componentWillReceiveProps(props) {
		this.updateListTitle(props.parentState.listInfo);
	}

	get listId() {
		return this.props.params == undefined ? undefined : this.props.params.splat;
	}

	componentDidUpdate() {
		$('[data-toggle="tooltip"]').tooltip()
	}

	@autobind
	updateListTitle(newData) {
		this.state.list_info = $.extend({}, this.state.list_info, {
			title: newData.title,
			privacy: newData.privacy
		});

		this.setState(this.state);
	}

	@autobind
	submitDeleteUserToServer(profile) {
		this.request.submitDeleteUserToServer = $.ajax({
			url: '/api/v1/list/delete',
			dataType: 'json',
			type: 'POST',
			data: {
				_method: 'DELETE',
				list_id: this.state.list_info.id,
				profile_id: profile.id
			},
			success: (data) => {
				if(data.error) {
					this.notify.danger(data.error).run();
					return;
				}

				this.notify.success('User has been removed from the list!').run();
				this.setState($.extend({}, this.state, data));
				this.profiles = data.profiles;
			},
			complete: () => {
				delete this.request.submitDeleteUserToServer;
			}
		});
	}

	@autobind
	submitSubscriptionToServer(button) {
		button.prop('disabled', true);

		this.request.submitSubscriptionToServer = $.ajax({
			url: '/api/v1/list/subscribe/' + this.state.list_info.id,
			dataType: 'json',
			type: 'POST',
			success: (data) => {
				if(data.error) {
					this.notify.danger(data.error).run();
					return;
				}

				this.notify.success('You have subscribed to the list!').run();
				this.setState($.extend({}, this.state, data));
				this.profiles = data.profiles;
			},
			complete: () => {
				button.prop('disabled', false);
				delete this.request.submitSubscriptionToServer;
			}
		});

	}

	@autobind
	submitUnsubscriptionToServer(button) {
		button.prop('disabled', true);

		this.request.submitUnsubscriptionToServer = $.ajax({
			url: '/api/v1/list/subscribe/' + this.state.list_info.id,
			dataType: 'json',
			type: 'POST',
			data: { _method: 'DELETE', },
			success: (data) => {
				if(data.error) {
					this.notify.danger(data.error).run();
					return;
				}

				this.notify.success('You have unsubscribed from the list!').run();
				this.setState($.extend({}, this.state, data));
				this.profiles = data.profiles;
			},
			complete: () => {
				button.prop('disabled', false);
				delete this.request.submitUnsubscriptionToServer;
			}
		});
	}

	@autobind
	submitManyUsersToServer(data) {
		this.request.submitManyUsersToServer = $.ajax({
			url: '/api/v1/list/add/many',
			dataType: 'json',
			type: 'POST',
			data: {
				search: data.search,
				description: data.description,
				list_id: this.listId
			},
			success: (data) => {
				this.setState($.extend({}, this.state, data));
				this.profiles = data.profiles;
			},
			complete: () => {
				delete this.request.submitManyUsersToServer;
			}
		});
	}

	@autobind
	actionChangePage(page) {
		this.setState($.extend({}, this.state, {page: page}));
	}

	@autobind
	displaySimilar(e) {
		let input = e.target;
		let searchValue = input.value;

		this.searchValue = searchValue;

		this.sortFilters();
	}

	@autobind
	toggleFilterButton() {
		if(this.filter == undefined) this.filter = 0;

		this.filter++;

		if(this.filter >= this.filterName.length)  this.filter = 0;
		$('.btn-filter-list').html('Show: ' + this.filterName[this.filter]);

		this.sortFilters();
	}

	sortFilters() {
		let searchValue, filter;
		let profiles = [];

		searchValue = this.searchValue;
		filter = this.filter;

		if(searchValue == undefined) searchValue = '';
		if(filter == undefined) filter = 0;

		this.profiles.map((val, index) => {
			let displayName = val.display_name;

			if(displayName.toLowerCase().indexOf(searchValue.toLowerCase()) == -1) return;

			switch(filter)
			{
				case 1:
					if(val.vac_bans == 0 && val.game_bans == 0) return;
					break;
				case 2:
					if(val.vac_bans > 0 || val.game_bans > 0) return;
					break;
			}

			profiles.push(val);
		});

		this.setState($.extend({}, this.state, {profiles: profiles}));
	}

	@autobind
	displayPerPage(e) {
		let input = e.target;
		let perPageValue = input.value;

		if(typeof(Storage) !== "undefined")
		{
			localStorage.vacstatusDisplayPerPage = perPageValue;
		}

		this.actionChangePage(0);
	}

	render() {
		let listInfo, profiles, page, author, privacy, listDetails,
			sortedList, listElement, eListAction, storageDisplayPerPage;

		listInfo = this.state.list_info;
		profiles = this.state.profiles;
		page = this.state.page;

		if(listInfo.author) author = <div><small>By: { listInfo.author }</small></div>;

		if(listInfo.privacy)
		{
			privacy = this.listPrivacy(listInfo.privacy);

			listDetails = (
				<div className="col-xs-12 col-md-6">
					<div className="list-extra-info text-right">
						<div className={"list-type text-" + privacy.color}>{ privacy.name } List</div>
						<div>Subscribed Users: { listInfo.sub_count }</div>
					</div>
				</div>
			);
		}

		storageDisplayPerPage = 20;

		if(typeof(Storage) !== "undefined" && localStorage.vacstatusDisplayPerPage != undefined)
		{
			storageDisplayPerPage = localStorage.vacstatusDisplayPerPage;
		}

		eListAction = [
			<div key="searchList" className="form-group">
				<label className="label-control">
					<strong>Search List</strong>
				</label>
				<input type="text" className="form-control" placeholder="Search for user in the list" onChange={this.displaySimilar} />
			</div>,
			<div key="extraControls" className="row">
				<div className="col-xs-6">
					<div className="form-group">
						<label className="label-control">
							<strong>Profiles per Page</strong>
						</label>
						<select className="form-control" onChange={this.displayPerPage} defaultValue={storageDisplayPerPage}>
							{[20, 30, 40, 60, 80, 100].map(function(val, index) {
								return <option key={index}>{ val }</option>
							})}
						</select>
					</div>
				</div>
				<div className="col-xs-6">
					<div className="form-group">
						<label className="label-control">
							<strong>Toggle Filter List</strong>
						</label>
						<button className="btn btn-block form-control btn-filter-list" onClick={ this.toggleFilterButton }>Show: All</button>
					</div>
				</div>
			</div>
		];

		if(this.authCheck)
		{
			if(this.props.search != undefined)
			{
				eListAction.push(
					<div key="addAllToList">
						<div className="row">
							<div className="col-xs-6 col-lg-12">
								<button className="btn btn-block btn-info" data-toggle="modal" data-target="#addAllUsers">Add All Users to List</button>
							</div>
						</div>
						<div id="searchUsers" className="hidden">{ profiles.map(function(p) { return p.steam_64_bit; }).join(" ") }</div>
					</div>
				);
			}

			if(listInfo.id !== undefined)
			{
				eListAction.push(
                 	<ListAction key="ListAction"
                 		addMany={this.submitManyUsersToServer}
                 		ListSubscribe={this.submitSubscriptionToServer}
                 		ListUnsubscribe={this.submitUnsubscriptionToServer}
                 		listInfo={listInfo}
                 	/>
                );
			}
		}

		sortedList = [];
		if(profiles !== null && profiles !== undefined)
		{
			for(var y = 0; y < Math.ceil(profiles.length / storageDisplayPerPage); y++)
			{
				for(var x = 0; x < storageDisplayPerPage; x++)
				{
					if(x === 0) sortedList[y] = [];

					var playerItem = profiles[(y * storageDisplayPerPage) + x];
					if(playerItem === undefined) break;

					sortedList[y].push(playerItem);
				}
			}
		}

		listElement = (
			<div className="container">
				<div className="row">
					<div className=" col-lg-9">
						<div className="row">
							<div className="col-xs-12 col-md-6">
								<h2 className="list-title">
									{ listInfo.title } { author }
								</h2>
							</div>
							{ listDetails }
						</div>

						<div className="table-responsive">
							<table className="table list-table">
								<thead>
									<tr>
										<th width="80"></th>
										<th>User</th>
										<th className="text-center" width="140">Last Ban Date</th>
										<th className="text-center hidden-sm" width="100">Vac Bans</th>
										<th className="text-center hidden-sm" width="100">Game Bans</th>
										<th className="text-center" width="100">Tracked By</th>
									</tr>
								</thead>
								<DisplayPage page={ page } list={ sortedList } listInfo={ listInfo } deleteUserFromList={ this.submitDeleteUserToServer }/>
							</table>
						</div>

						<ListPagination listChangePage={this.actionChangePage} page={page} list={sortedList}/>
					</div>
					<div className="col-lg-3">
						<div className="list-actions visible-lg-block">
							<div className="list-action-container">
								<hr className="divider" />
								{ eListAction }
							</div>
						</div>
					</div>
				</div>
			</div>
		);

		return (
			<div id="list" className="list-page">
				<div className="list-action-bar hidden-lg">
					<div className="container">
						<div className="row">
							<div className="col-xs-12">
								<a href="#" data-toggle="collapse" data-target="#list-actions"><span className="fa fa-bars"></span>&nbsp; Advanced Options</a>
								<div id="list-actions" className="list-actions collapse">
									<div className="list-action-container">
										<hr className="divider" />
										{ eListAction }
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				{ listElement }
			</div>
		);
	}
}

class ListAction extends BasicComp {
	constructor(props)
	{
		super(props);

		this.addMany = this.addMany.bind(this);
		this.doSub = this.doSub.bind(this);
		this.doUnsub = this.doUnsub.bind(this);
	}

	doSub(e)
	{
		this.props.ListSubscribe($(e.target));
	}

	doUnsub(e)
	{
		this.props.ListUnsubscribe($(e.target));
	}

	addMany(e)
	{
		e.preventDefault();

		let search = this.refs.search.getDOMNode().value.trim();
		let description = this.refs.description.getDOMNode().value.trim();

		this.props.addMany({search: search, description: description});

		this.refs.search.getDOMNode().value = '';
		this.refs.description.getDOMNode().value = '';
	}

	render()
	{
		let listInfo, editList, subButton, addUsers;

		listInfo = this.props.listInfo;

		if(listInfo == null || listInfo.id == null)
		{
			return <div></div>;
		}

		if(listInfo.my_list) {
			editList = (
				<div className="col-xs-6 col-lg-12">
					<div className="form-group">
						<button className="btn btn-block btn-info" data-toggle="modal" data-target="#editListModal">Edit List</button>
					</div>
				</div>
			);

			addUsers = (
				<div className="col-xs-6 col-lg-12">
					<form onSubmit={this.addMany}>
						<div className="form-group">
							<label className="label-control">
								<strong>Add Users to List</strong>
							</label>
							<textarea ref="search" className="form-control" rows="10"
placeholder="2 ways to search: =================================
- type in steam URL/id/profile and split them in spaces or newlines or both =================================
- Type 'status' on console and paste the output here"></textarea>
						</div>
						<div className="form-group">
							<textarea ref="description" className="form-control" rows="3" placeholder="A little description to help remember"></textarea>
						</div>
						<button className="btn btn-block btn-primary form-control">Add Users</button>
					</form>
				</div>
			);
		}

		subButton = (
			<div className="col-xs-6 col-lg-12">
				<div className="form-group">
					<button className="btn btn-block" disabled="disabled">Subscribe to List</button>
					<div className="text-center">
						<small><i>Please go to settings and verify email</i></small>
					</div>
				</div>
			</div>
		);

		if(listInfo.can_sub)
		{
			subButton = (
				<div className="col-xs-6 col-lg-12">
					<div className="form-group">
						<button onClick={ this.doSub } className="btn btn-block btn-primary">Subscribe to List</button>
					</div>
				</div>
			);

			if(listInfo.subscription !== null) 
			{
				subButton = (
					<div className="col-xs-6 col-lg-12">
						<div className="form-group">
							<button onClick={ this.doUnsub } className="btn btn-block btn-danger">Unubscribe to List</button>
						</div>
					</div>
				);
			}
		}

		return (
			<div className="row">
				{ editList }
				{ subButton }
				{ addUsers }
			</div>
		);
	}
}

class ListPagination extends BasicComp {
	changePage(page)
	{
		this.props.listChangePage(page);
	}

	render()
	{
		var list, page, pagePrev, pageNext, pageList;

		list = this.props.list;
		page = this.props.page;
		page = page <= 1 || page > list.length ? 1 : page;

		if(list.length <= 1 || list[0] === undefined) return <div></div>;

		pagePrev = (
			<li className={ page != 1 ? "" : "disabled" }>
				<a onClick={ page != 1 ? this.changePage.bind(this, page - 1) : ""}>
					<span>&laquo;</span>
				</a>
			</li>
		);

		pageNext = (
			<li className={ page < list.length ? "" : "disabled" }>
				<a onClick={ page < list.length ? this.changePage.bind(this, page + 1) : "" }>
					<span>&raquo;</span>
				</a>
			</li>
		);

		pageList = [];

		for(var p = 1; p <= list.length; p++)
		{
			pageList.push(<li key={ p } className={p == page ? "active" : "" }><a onClick={this.changePage.bind(this, p)}>{ p }</a></li>);
		}

		return (
		<nav className="pull-right">
			<ul className="pagination">
				{ pagePrev }
				{ pageList }
				{ pageNext }
			</ul>
		</nav>);
	}
}

class DisplayPage extends BasicComp {

	sendDeleteUserFromList(profile)
	{
		this.props.deleteUserFromList(profile);
	}

	render()
	{
		let list, page, listInfo, pagedList;

		list = this.props.list;
		page = this.props.page;
		listInfo = this.props.listInfo;

		page = page <= 1 || page > list.length ? 1 : page;

		if(listInfo.title == null)
		{
			return (
				<tbody>
					<tr>
						<td colSpan="6" className="text-center">
							<b>Loading List....</b>
						</td>
					</tr>
				</tbody>
			);
		}

		if(list[0] === undefined)
		{
			return (
				<tbody>
					<tr>
						<td colSpan="6" className="text-center">
							<i>This list is empty</i>
						</td>
					</tr>
				</tbody>
			);
		}

		pagedList = list[page - 1].map((profile, index) =>
		{
			let auth, specialColors, profile_description;

			if(this.authCheck) {
				if(listInfo.my_list) {
					auth = (
						<span className="pointer userListModify open-addUserModal" onClick={ this.sendDeleteUserFromList.bind(this, profile) } data-id={ profile.id }>
							<span className="fa fa-minus faText-align text-danger"></span>
						</span>
					);
				} else {
					auth = (
						<a className="userListModify open-addUserModal" href="#addUserModal" data-toggle="modal" data-id={ profile.id }>
							<span className="fa fa-plus faText-align text-primary"></span>
						</a>
					);
				}
			}

			specialColors = this.userTitle(profile);

			if(profile.profile_description)
			{
				profile_description = <i className="fa fa-eye pointer" data-toggle="tooltip" data-placement="right" title={ profile.profile_description }></i>
			}

			return (
				<tr key={ profile.id }>
					<td className="user_avatar">
						{ auth }<a className={specialColors} href={"/u/" + profile.steam_64_bit} target="_blank"><img src={profile.avatar_thumb} /></a>
					</td>
					<td className="user_name">
						{ profile_description } <a className={specialColors} href={"/u/" + profile.steam_64_bit} target="_blank">{profile.display_name}</a>
						<div className="username_subtext">{ profile.steam_64_bit }{ profile.added_at ? ' — ' + profile.added_at : '' }</div>
					</td>
					<td className="user_last_ban_day text-center">
						<span className={"text-" + (profile.vac_bans > 0 || profile.game_bans > 0 ? "danger" : "")}>
							{ profile.vac_bans > 0 || profile.game_bans > 0 ? profile.last_ban_date : "" }
						</span>
					</td>
					<td className="user_vac_bans text-center hidden-sm">
						{ profile.vac_bans > 0 ? profile.vac_bans : '-'}
					</td>
					<td className="user_game_bans text-center hidden-sm">
						{ profile.game_bans > 0 ? profile.game_bans : '-'}
					</td>	
					<td className="user_track_number text-center">
						{ profile.total }
					</td>
				</tr>
			);
		});

		return <tbody>{ pagedList }</tbody>;
	}
}