'use strict';

class Footer extends React.Component {
	render () {
		return (
	        <div>
		        <div className="footer">
					<div className="container">
						<div className="row">
							<div className="col-xs-12 col-md-6 col-md-push-6">
								<ul className="left-footer">
									<li><a href="/privacy">Privacy Policy</a></li>
									<li><a href="/contact">Contact</a></li>
									<li><a target="_blank" href="https://github.com/jung3o/VacStatus">Github</a></li>
									<li><a target="_blank" href="https://trello.com/vacstatus">Trello</a></li>
								</ul>
							</div>

							<div className="col-xs-12 col-md-6 col-md-pull-6">
								<div className="right-footer copyright">
									<div>&copy; 2015 VacStatus.</div>
									<div>Powered By <a href="http://steampowered.com" target="_blank">Steam</a></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div className="modal fade" id="searchModal" tabIndex="-1" role="dialog">
					<div className="modal-dialog">
						<div className="modal-content">
							<div className="modal-header">
								<button type="button" className="close" data-dismiss="modal"><span>&times;</span></button>
								<h4 className="modal-title">Look Up Users</h4>
							</div>
							<form action="/search" method="post">
								<div className="modal-body">
									<div className="form-group">
										<textarea
											name="search"
											cols="30"
											rows="10"
											className="form-control"
											placeholder="2 ways to search:
 - type in steam URL/id/profile and split them in spaces or newlines or both
 - Type 'status' on console and paste the output here" />
									</div>
								</div>
								<div className="modal-footer">
									<input type="hidden" name="_token" value={ _token } />
									<button type="button" className="btn btn-default" data-dismiss="modal">Close</button>
									<button type="submit" className="btn btn-primary">Search</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		);
	}
}