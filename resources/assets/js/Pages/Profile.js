'use strict';

import React from 'react';
import BasicComp from '../BasicComp';

export default class Profile extends BasicComp {
	componentDidMount() {
		this.fetchProfile(this.props.params.steamId);
	}

	componentWillReceiveProps(props) {
		this.fetchProfile(props.params.steamId);
	}

	fetchProfile(steamId) {
		this.request.fetchProfile = $.ajax({
			url: '/api/v1/profile/'+steamId,
			dataType: 'json',
			success: (data) => {
				this.setState(data);
			},
			complete: () => {
				delete this.request.fetchProfile;
			}
		});
	}

	render() {
		let state;

		state = this.state;

		return (
			<div>
				<div id="profile" className="profile-page">
					<div className="profile-start">
						<ProfileHeader profile={ state }/>
						<ProfileBadge profile={ state }/>
						<ProfileBody profile={ state }/>
						<ProfileVacStatus profile={ state }/>
					</div>
				</div>
				<div className="container">
					<div className="row">
						<div className="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
							<div id="disqus_thread" className="disqus_thread"></div>
							<ProfileDisqus profile={ state }/>
						</div>
					</div>
				</div>
			</div>
		);
	}
}

class ProfileHeader extends BasicComp {
	componentWillReceiveProps(props) {
		this.setState(props.profile);
	}

	render() {
		let state = this.state;
		let specialColors = this.userTitle(state);
		let privacy = this.listPrivacy(4 - state.privacy);

		let auth;

		if(this.authCheck) auth = (
			<a className="open-addUserModal" href="#addUserModal" data-toggle="modal" data-id={ state.id }>
				<span className="fa fa-plus faText-align"></span>
			</a>
		);

		return (
			<div className="profile-header">
				<div className="container">
					<div className="row">
						<div className="col-xs-12 col-md-3 col-lg-2 col-lg-offset-1">
							<div className="profile-avatar">
								<img className="img-responsive" src={ state.avatar } />
							</div>
						</div>
						<div className="col-xs-12 col-md-9">
							<div className="row">
								<div className="col-xs-12">
									<div className="profile-username">
										{ auth }
										<span className={ specialColors + "-name"}> { state.display_name }</span>
									</div>
								</div>
							</div>
							<div className="row">
								<div className="col-xs-12 col-md-2">
									<div className="profile-steam">
										<a href={"http://steamcommunity.com/profiles/" + state.steam_64_bit} target="_blank">
											<span className="fa fa-steam"></span>
										</a>
									</div>
								</div>
								<div className="col-xs-12 col-sm-6 col-md-4">
									<ul className="profile-info">
										<li>
											<div className="row">
												<div className="col-xs-6 text-right"><strong>Creation</strong></div>
												<div className="col-xs-6">{ state.profile_created }</div>
											</div>
										</li>
										<li>
											<div className="row">
												<div className="col-xs-6 text-right"><strong>Steam3 ID</strong></div>
												<div className="col-xs-6">{"U:1:" + state.small_id }</div>
											</div>
										</li>
										<li>
											<div className="row">
												<div className="col-xs-6 text-right"><strong>Steam ID 32</strong></div>
												<div className="col-xs-6">{ state.steam_32_bit }</div>
											</div>
										</li>
										<li>
											<div className="row">
												<div className="col-xs-6 text-right"><strong>Steam ID 64</strong></div>
												<div className="col-xs-6">{ state.steam_64_bit }</div>
											</div>
										</li>
									</ul>
								</div>
								<div className="col-xs-12 col-sm-6 col-md-6 col-lg-5">
									<ul className="profile-info">
										<li>
											<div className="row">
												<div className="col-xs-6 text-right"><strong>Profile Status</strong></div>
												<div className="col-xs-6"><div className={"text-" + privacy.color}>{ privacy.name }</div></div>
											</div>
										</li>
										<li>
											<div className="row">
												<div className="col-xs-6 text-right"><strong>VAC / Game Ban</strong></div>
												<div className="col-xs-6">
													<div className={"text-" + (state.vac_bans > 0 || state.game_bans > 0 ? 'danger' : 'success') }>
														{ state.vac_bans > 0 || state.game_bans > 0 ? state.last_ban_date : 'Normal'}
													</div>
												</div>
											</div>
										</li>
										<li>
											<div className="row">
												<div className="col-xs-6 text-right"><strong>Trade ban</strong></div>
												<div className="col-xs-6"><div className={"text-" + (state.trade ? 'danger' : 'success')}>{ state.trade ? 'Banned' : 'Normal' }</div></div>
											</div>
										</li>
										<li>
											<div className="row">
												<div className="col-xs-6 text-right"><strong>Community Ban</strong></div>
												<div className="col-xs-6"><div className={"text-" + (state.community ? 'danger' : 'success')}>{ state.community ? 'Banned' : 'Normal' }</div></div>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		);
	}
}

class ProfileBadge extends BasicComp {
	componentWillReceiveProps(props) {
		this.setState(props.profile);
	}

	render() {
		let state = this.state;

		return (
			<div className="profile-badge">
				<div className="container">
					<div className="row">
						<div className="col-xs-12">
							{ state.site_admin >= 1 ? <div className="label label-warning">Admin</div> : '' }
							{ state.donation >= 1 ? <div className="label label-success">Donator</div> : '' }
							{ state.beta >= 1 ? <div className="label label-primary">Beta</div> : '' }
						</div>
					</div>
				</div>
			</div>
		);
	}
}

class ProfileBody extends BasicComp {
	componentWillReceiveProps(props) {
		this.setState(props.profile);
	}

	render() {
		let state = this.state;
		let alias_history, alias_recent;

		if(state.profile_old_alias) {
			alias_history = state.profile_old_alias.map((alias, index) => {
				return (
					<tr key={index}>
						<td>{ alias.timechanged }</td>
						<td>{ alias.newname }</td>
					</tr>
				);
			});
		} 

		if(state.alias) {
			alias_recent = state.alias.map((alias, index) => {
				return (
					<tr key={index}>
						<td>{ alias.timechanged }</td>
						<td>{ alias.newname }</td>
					</tr>
				);
			});
		}

		return (
			<div className="profile-body">
				<div className="container">
					<div className="row">
						<div className="col-xs-12 col-lg-10 col-lg-offset-1">
							<div className="title">
								User Aliases
							</div>
						</div>
						<div className="col-xs-12 col-md-6 col-lg-5 col-lg-offset-1">
							<div className="table-responsive">
								<table className="table">
									<thead>
										<tr>
											<th colSpan="2">Alias History</th>
										</tr>
										<tr>
											<th className="table-timedisplay">Used On</th>
											<th>Username</th>
										</tr>
									</thead>
									<tbody>
										{ alias_history }
									</tbody>
								</table>
							</div>
						</div>
						<div className="col-xs-12 col-md-6 col-lg-5">
							<div className="table-responsive">
								<table className="table">
									<thead>
										<tr>
											<th colSpan="2">Recent Aliases</th>
										</tr>
										<tr>
											<th className="table-timedisplay">Used On</th>
											<th>Username</th>
										</tr>
									</thead>
									<tbody>
										{ alias_recent }
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<hr className="divider" />
				</div>
			</div>
		);
	}
}

class ProfileVacStatus extends BasicComp {
	componentWillReceiveProps(props) {
		this.setState(props.profile);
	}

	render() {
		let state = this.state;
		let beingTrackedOn, authorOf;

		if(state.being_tracked_on != undefined) beingTrackedOn = state.being_tracked_on.map((data, index) => {
			return (
				<tr key={ index }>
					<td>{ data.added_at }</td>
					<td><a target="_blank" href={ "/list/" + data.id }>{ data.title }</a></td>
				</tr>
			);
		});

		if(state.author_of != undefined) authorOf = state.author_of.map((data, index) => {
			return (
				<tr key={ index }>
					<td>{ data.created_at }</td>
					<td><a target="_blank" href={ "/list/" + data.id }>{ data.title }</a></td>
				</tr>
	        );
		});

		return (
			<div className="profile-vacstatus">
				<div className="container">
					<div className="row">
						<div className="col-xs-12 col-lg-10 col-lg-offset-1">
							<div className="title">
								VacStatus Account
							</div>
						</div>
					</div>
					<div className="row">
						<div className="col-xs-12 col-md-6 col-lg-5 col-lg-offset-1">
							<div className="table-responsive">
								<table className="table">
									<thead>
										<tr>
											<th className="text-center" colSpan="2">Public Lists Being Tracked In</th>
										</tr>
										<tr>
											<th className="table-timedisplay">Added On</th>
											<th>Title</th>
										</tr>
									</thead>
									<tbody>{ beingTrackedOn }</tbody>
								</table>
							</div>
						</div>
						<div className="col-xs-12 col-md-6 col-lg-5">
							<div className="table-responsive">
								<table className="table">
									<thead>
										<tr>
											<th className="text-center" colSpan="2">{ state.display_name }'s Public Lists</th>
										</tr>
										<tr>
											<th className="table-timedisplay">Created At</th>
											<th>Title</th>
										</tr>
									</thead>
									<tbody>{ authorOf }</tbody>
								</table>
							</div>
						</div>
					</div>
					<hr className="divider" />
					<div className="row">
						<div className="col-xs-12 col-md-4 col-md-offset-2">
							<h3 className="title">Extra Info</h3>
							<div className="content text-center">
								<div className="row">
									<div className="col-xs-6">
										<strong># of VAC Bans</strong><br />
											{ state.vac_bans }
									</div>
									<div className="col-xs-6">
										<strong># of Game Bans</strong><br />
											{ state.game_bans }
									</div>
								</div>
							</div>
						</div>
						<div className="col-xs-12 col-md-4">
							<h3 className="title">VacStatus Info</h3>
							<div className="content text-center">
								<div className="row">
									<div className="col-xs-6">
										<strong>First Checked</strong><br />
											{ state.created_at }
									</div>
									<div className="col-xs-6">
										<strong>Times Added</strong><br />
											{ state.number ? state.number : 0 } <sub>{ state.time ? "(" + state.time + ")" : '' }</sub>
									</div>
								</div>
							</div>
						</div>
					</div>
					<hr className="divider" />
				</div>
			</div>
		);
	}
}

class ProfileDisqus extends BasicComp {
	constructor(props) {
		super(props);

		let state = props.profile;
		
		if(document.getElementById('disqusJS') != null) return;

		var disqus_identifier = state.steam_64_bit;

		(function () {
			var dsq = document.createElement('script');

			dsq.id = "disqusJS";
			dsq.type = 'text/javascript';
			dsq.async = true;
			dsq.src = '//vbanstatus.disqus.com/embed.js';

			(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		})();
	}

	componentWillReceiveProps(props) {
		this.setState(props.profile);
	}

	render() {
		if(typeof DISQUS != 'undefined' && document.getElementById('disqus_thread') != null)
		{
			var state = this.state;

			DISQUS.reset({
				reload: true,
				config: function()
				{  
					this.page.identifier = state.steam_64_bit;
					this.page.url = window.location.href + '#!newthread';
					this.page.title = 'VacStatus ['+ state.steam_64_bit +']';
				}
			});
		}

		return <div></div>;
	}
}