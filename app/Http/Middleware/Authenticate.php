<?php

namespace VacStatus\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use VacStatus\Models\User;

class Authenticate {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($this->auth->guest())
		{
			if ($request->ajax()) return response('Unauthorized.', 401);
			else {
				$thisRoute = explode('.', $request->route()->getName());
				if($thisRoute[0] == 'api')
				{
					$userKey = $request->input('_key');
					if($userKey && !empty($userKey))
					{
						$user = User::where('user_key', $userKey)->first();

						if(isset($user->id)) return $next($request);
					}
					return ['error' => 'forbidden'];
				}

				return redirect()->guest('auth/login');
			}
		}

		return $next($request);
	}

}
