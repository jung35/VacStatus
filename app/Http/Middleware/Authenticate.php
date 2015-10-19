<?php

namespace VacStatus\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use VacStatus\Models\User;

use Input;

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

			$thisRoute = explode('/', $request->route()->getURI());
			if($thisRoute[0] == 'api')
			{
				if(Input::has('_key'))
				{
					$user = User::where('user_key', Input::get('_key'))->first();

					if(isset($user->id)) return $next($request);
				}

				return Response()->json(['error' => 'forbidden'], 403);
			}

			return redirect()->guest('auth/login');
		} 

		return $next($request);
	}

}
